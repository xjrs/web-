# 项目任务管理系统数据库设计

## 概述
本文档描述了项目任务管理系统的完整数据库设计，包含用户管理、项目管理、任务管理以及各种关系表。

## 核心表结构

### 1. 用户表 (users)
**文件**: `2014_10_12_000000_create_users_table.php`

| 字段 | 类型 | 描述 |
|------|------|------|
| id | bigint | 主键 |
| name | varchar | 用户名称 |
| email | varchar | 邮箱（唯一） |
| email_verified_at | timestamp | 邮箱验证时间 |
| password | varchar | 密码 |
| remember_token | varchar | 记住登录令牌 |
| created_at | timestamp | 创建时间 |
| updated_at | timestamp | 更新时间 |

### 2. 项目表 (projects)
**文件**: `2025_06_19_000001_create_projects_table.php`

| 字段 | 类型 | 描述 |
|------|------|------|
| id | bigint | 主键 |
| name | varchar | 项目名称 |
| description | text | 项目描述 |
| manager_id | bigint | 项目经理ID (外键到users表) |
| participant_count | int | 项目参与人数 |
| status | enum | 项目状态 (planning, active, on_hold, completed, cancelled) |
| priority | enum | 项目优先级 (low, medium, high, urgent) |
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
| status | enum | 任务状态 (pending, in_progress, completed) |
| priority | enum | 任务优先级 (low, medium, high, urgent) |
| created_at | timestamp | 任务创建时间 |
| expected_start_time | timestamp | 预计开始时间 |
| actual_start_time | timestamp | 实际开始时间 |
| expected_end_time | timestamp | 预计结束时间 |
| actual_end_time | timestamp | 实际结束时间 |
| updated_at | timestamp | 更新时间 |
| project_id | bigint | 所属项目ID（可选，外键到projects表） |
| manager_id | bigint | 任务负责人ID（外键到users表） |

### 4. 用户联系人表 (user_contacts)
**文件**: `2025_06_19_000003_create_user_contacts_table.php`

| 字段 | 类型 | 描述 |
|------|------|------|
| id | bigint | 主键 |
| user_id | bigint | 用户ID (外键到users表) |
| contact_user_id | bigint | 联系人用户ID (外键到users表) |
| contact_name | varchar | 联系人备注名称 |
| notes | text | 备注信息 |
| created_at | timestamp | 创建时间 |
| updated_at | timestamp | 更新时间 |

### 5. 用户项目角色表 (user_project_roles)
**文件**: `2025_06_19_000004_create_user_project_roles_table.php`

| 字段 | 类型 | 描述 |
|------|------|------|
| id | bigint | 主键 |
| user_id | bigint | 用户ID (外键到users表) |
| project_id | bigint | 项目ID (外键到projects表) |
| role | varchar | 用户在项目中的角色 |
| is_manager | boolean | 是否为项目负责人 |
| responsibilities | text | 职责描述 |
| joined_at | timestamp | 加入项目时间 |
| left_at | timestamp | 离开项目时间 |
| created_at | timestamp | 创建时间 |
| updated_at | timestamp | 更新时间 |

### 6. 用户任务分配表 (user_task_assignments)
**文件**: `2025_06_19_000005_create_user_task_assignments_table.php`

| 字段 | 类型 | 描述 |
|------|------|------|
| id | bigint | 主键 |
| user_id | bigint | 用户ID (外键到users表) |
| task_id | bigint | 任务ID (外键到tasks表) |
| role | varchar | 角色（默认：assignee） |
| work_description | text | 承担的工作描述 |
| assigned_at | timestamp | 分配时间 |
| created_at | timestamp | 创建时间 |
| updated_at | timestamp | 更新时间 |

### 7. 项目任务关系表 (project_task_relations)
**文件**: `2025_06_19_000006_create_project_task_relations_table.php`

| 字段 | 类型 | 描述 |
|------|------|------|
| id | bigint | 主键 |
| project_id | bigint | 项目ID (外键到projects表) |
| task_id | bigint | 任务ID (外键到tasks表) |
| is_critical | boolean | 是否为关键任务 |
| dependency_level | int | 依赖级别 |
| completion_weight | decimal(5,2) | 任务在项目中的完成权重 |
| impact_description | text | 任务对项目的影响描述 |
| created_at | timestamp | 创建时间 |
| updated_at | timestamp | 更新时间 |

### 8. 任务依赖表 (task_dependencies)
**文件**: `2025_06_19_000007_create_task_dependencies_table.php`

| 字段 | 类型 | 描述 |
|------|------|------|
| id | bigint | 主键 |
| task_id | bigint | 当前任务ID (外键到tasks表) |
| depends_on_task_id | bigint | 依赖的任务ID (外键到tasks表) |
| dependency_type | enum | 依赖类型 (finish_to_start, start_to_start, finish_to_finish, start_to_finish) |
| lag_days | int | 滞后天数 |
| description | text | 依赖关系描述 |
| created_at | timestamp | 创建时间 |
| updated_at | timestamp | 更新时间 |

## 索引设计

### 项目表 (projects)
- 复合索引：[status, priority]
- 单列索引：manager_id

### 任务表 (tasks)
- 复合索引：[status, priority]
- 复合索引：[expected_start_time, expected_end_time]
- 单列索引：project_id

### 用户联系人表 (user_contacts)
- 唯一索引：[user_id, contact_user_id]
- 单列索引：user_id, contact_user_id

### 用户项目角色表 (user_project_roles)
- 唯一索引：[user_id, project_id]
- 单列索引：user_id, project_id, role

### 用户任务分配表 (user_task_assignments)
- 单列索引：user_id, task_id, role, assigned_at

### 项目任务关系表 (project_task_relations)
- 唯一索引：[project_id, task_id]
- 单列索引：project_id, task_id, is_critical

### 任务依赖表 (task_dependencies)
- 唯一索引：[task_id, depends_on_task_id]
- 单列索引：task_id, depends_on_task_id, dependency_type