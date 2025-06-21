# 项目任务管理系统数据库设计

## 概述
本文档描述了项目任务管理系统的完整数据库设计，包含用户管理、项目管理、任务管理以及各种关系表。

## 核心表结构

### 1. 用户表 (users)
现有表，包含基本用户信息：
- `id` - 主键
- `name` - 用户名
- `email` - 邮箱
- `password` - 密码
- `avatar` - 头像
- `created_at`, `updated_at` - 时间戳

### 2. 项目表 (projects)
**文件**: `2025_06_19_000001_create_projects_table.php`

| 字段 | 类型 | 描述 |
|------|------|------|
| id | bigint | 主键 |
| name | varchar | 项目名称 |
| description | text | 项目描述 |
| manager_id | bigint | 项目经理ID (外键到users表) |
| participant_count | int | 项目参与人数 |
| status | enum | 项目状态 (planning/active/on_hold/completed/cancelled) |
| priority | enum | 项目优先级 (low/medium/high/urgent) |
| total_tasks | int | 总任务数 |
| created_at | timestamp | 项目创建时间 |
| expected_start_time | timestamp | 预计开始时间 |
| actual_start_time | timestamp | 实际开始时间 |
| expected_end_time | timestamp | 预计结束时间 |
| actual_end_time | timestamp | 实际结束时间 |
| updated_at | timestamp | 更新时间 |

### 3. 任务表 (tasks)
**文件**: `2025_06_19_000002_create_tasks_table.php`

| 字段 | 类型 | 描述 |
|------|------|------|
| id | bigint | 主键 |
| title | varchar | 任务标题 |
| description | text | 任务描述 |
| status | enum | 任务状态 (pending/in_progress/completed) |
| priority | enum | 任务优先级 (low/medium/high/urgent) |
| created_at | timestamp | 任务创建时间 |
| expected_start_time | timestamp | 预计开始时间 |
| actual_start_time | timestamp | 实际开始时间 |
| expected_end_time | timestamp | 预计结束时间 |
| actual_end_time | timestamp | 实际结束时间 |
| updated_at | timestamp | 更新时间 |
| project_id | bigint | 所属项目ID (外键到projects表，可为空) |

## 关系表

### 4. 用户通讯录表 (user_contacts)
**文件**: `2025_06_19_000003_create_user_contacts_table.php`

记录用户之间的联系关系：
- `user_id` - 用户ID
- `contact_user_id` - 联系人用户ID
- `contact_name` - 联系人备注名称


### 5. 用户项目角色表 (user_project_roles)
**文件**: `2025_06_19_000004_create_user_project_roles_table.php`

记录用户在项目中的角色：
- `user_id` - 用户ID
- `project_id` - 项目ID
- `role` - 角色（支持预设角色和自定义角色）
- `is_manager` - 是否为项目负责人
- `responsibilities` - 职责描述
- `joined_at` - 加入时间


### 6. 用户任务分配表 (user_task_assignments)
**文件**: `2025_06_19_000005_create_user_task_assignments_table.php`

记录用户在任务中的角色和工作：
- `user_id` - 用户ID
- `task_id` - 任务ID
- `role` - 角色（支持预设角色和自定义角色）
- `is_manager` - 是否为任务负责人
- `work_description` - 工作描述
- `assigned_at` - 分配时间

### 7. 项目任务关系表 (project_task_relations)
**文件**: `2025_06_19_000006_create_project_task_relations_table.php`

记录任务在项目中的角色：
- `project_id` - 项目ID
- `task_id` - 任务ID
- `is_critical` - 是否关键任务
- `completion_weight` - 完成权重

### 8. 任务依赖关系表 (task_dependencies)
**文件**: `2025_06_19_000007_create_task_dependencies_table.php`

管理任务间的依赖关系：
- `task_id` - 当前任务ID
- `depends_on_task_id` - 依赖的任务ID
- `dependency_type` - 依赖类型：
  - `finish_to_start` - 完成-开始：前置任务完成后，当前任务才能开始
  - `start_to_start` - 开始-开始：前置任务开始后，当前任务才能开始
  - `finish_to_finish` - 完成-完成：前置任务完成后，当前任务才能完成
  - `start_to_finish` - 开始-完成：前置任务开始后，当前任务才能完成
- `lag_days` - 滞后天数（正数表示延后，负数表示提前）

## 数据库关系图

```
users (用户表)
├── projects (项目表) - manager_id
├── tasks (任务表) - user_id
├── user_contacts (通讯录) - user_id, contact_user_id
├── user_project_roles (用户项目角色) - user_id
└── user_task_assignments (用户任务分配) - user_id

projects (项目表)
├── tasks (任务表) - project_id
├── user_project_roles (用户项目角色) - project_id
└── project_task_relations (项目任务关系) - project_id

tasks (任务表)
├── user_task_assignments (用户任务分配) - task_id
├── project_task_relations (项目任务关系) - task_id
└── task_dependencies (任务依赖) - task_id, depends_on_task_id
```

## 索引策略

1. **主键索引**: 所有表的id字段
2. **外键索引**: 所有外键字段
3. **复合索引**: 
   - `projects`: (status, priority)
   - `tasks`: (status, priority), due_date
   - `user_project_roles`: (role, is_active)
   - `user_task_assignments`: (role, status)

## 约束条件

1. **外键约束**: 确保数据完整性
2. **唯一约束**: 
   - `user_contacts`: (user_id, contact_user_id)
   - `user_project_roles`: (user_id, project_id, is_active)
   - `project_task_relations`: (project_id, task_id)
   - `task_dependencies`: (task_id, depends_on_task_id)

## 使用建议

1. **项目创建流程**:
   - 创建项目记录
   - 添加项目经理到user_project_roles
   - 逐步添加项目成员

2. **任务分配流程**:
   - 创建任务记录
   - 建立project_task_relations关系
   - 分配用户到user_task_assignments

3. **权限控制**:
   - 基于user_project_roles判断项目权限
   - 基于user_task_assignments判断任务权限

4. **数据统计**:
   - 通过project_task_relations统计项目进度
   - 通过user_task_assignments统计用户工作量