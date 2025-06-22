<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // 清空所有表（按依赖关系顺序）
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('task_dependencies')->truncate();
        DB::table('project_task_relations')->truncate();
        DB::table('user_task_assignments')->truncate();
        DB::table('user_project_roles')->truncate();
        DB::table('user_contacts')->truncate();
        DB::table('tasks')->truncate();
        DB::table('projects')->truncate();
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. 创建用户数据
        $users = [
            [
                'id' => 1,
                'name' => '张三',
                'email' => 'zhangsan@example.com',
                'password' => Hash::make('password'),
                'avatar' => 'avatars/user1.svg',
                'created_at' => Carbon::now()->subDays(30),
                'updated_at' => Carbon::now()->subDays(30),
            ],
            [
                'id' => 2,
                'name' => '李四',
                'email' => 'lisi@example.com',
                'password' => Hash::make('password'),
                'avatar' => 'avatars/user2.svg',
                'created_at' => Carbon::now()->subDays(25),
                'updated_at' => Carbon::now()->subDays(25),
            ],
            [
                'id' => 3,
                'name' => '王五',
                'email' => 'wangwu@example.com',
                'password' => Hash::make('password'),
                'avatar' => 'avatars/user3.svg',
                'created_at' => Carbon::now()->subDays(20),
                'updated_at' => Carbon::now()->subDays(20),
            ],
            [
                'id' => 4,
                'name' => '赵六',
                'email' => 'zhaoliu@example.com',
                'password' => Hash::make('password'),
                'avatar' => 'avatars/user4.svg',
                'created_at' => Carbon::now()->subDays(15),
                'updated_at' => Carbon::now()->subDays(15),
            ],
            [
                'id' => 5,
                'name' => '钱七',
                'email' => 'qianqi@example.com',
                'password' => Hash::make('password'),
                'avatar' => 'avatars/user5.svg',
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(10),
            ],
            [
                'id' => 6,
                'name' => '孙八',
                'email' => 'sunba@example.com',
                'password' => Hash::make('password'),
                'avatar' => 'avatars/user6.svg',
                'created_at' => Carbon::now()->subDays(8),
                'updated_at' => Carbon::now()->subDays(8),
            ],
            [
                'id' => 7,
                'name' => '周九',
                'email' => 'zhoujiu@example.com',
                'password' => Hash::make('password'),
                'avatar' => 'avatars/user7.svg',
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'id' => 8,
                'name' => '吴十',
                'email' => 'wushi@example.com',
                'password' => Hash::make('password'),
                'avatar' => 'avatars/user8.svg',
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(3),
            ],
        ];
        DB::table('users')->insert($users);

        // 2. 创建项目数据
        $projects = [
            [
                'id' => 1,
                'name' => '电商平台开发',
                'description' => '构建一个现代化的电商平台，包含用户管理、商品管理、订单处理等核心功能',
                'manager_id' => 1,
                'participant_count' => 5,
                'status' => 'active',
                'priority' => 'high',
                'total_tasks' => 12,
                'created_at' => Carbon::now()->subDays(25),
                'expected_start_time' => Carbon::now()->subDays(20),
                'actual_start_time' => Carbon::now()->subDays(18),
                'expected_end_time' => Carbon::now()->addDays(45),
                'actual_end_time' => null,
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'id' => 2,
                'name' => '移动应用开发',
                'description' => '开发跨平台移动应用，支持iOS和Android系统',
                'manager_id' => 2,
                'participant_count' => 4,
                'status' => 'planning',
                'priority' => 'medium',
                'total_tasks' => 8,
                'created_at' => Carbon::now()->subDays(15),
                'expected_start_time' => Carbon::now()->addDays(5),
                'actual_start_time' => null,
                'expected_end_time' => Carbon::now()->addDays(60),
                'actual_end_time' => null,
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'id' => 3,
                'name' => '数据分析系统',
                'description' => '构建企业级数据分析和可视化系统',
                'manager_id' => 3,
                'participant_count' => 3,
                'status' => 'completed',
                'priority' => 'low',
                'total_tasks' => 6,
                'created_at' => Carbon::now()->subDays(60),
                'expected_start_time' => Carbon::now()->subDays(55),
                'actual_start_time' => Carbon::now()->subDays(53),
                'expected_end_time' => Carbon::now()->subDays(10),
                'actual_end_time' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'id' => 4,
                'name' => '云服务迁移',
                'description' => '将现有系统迁移到云平台，提升系统可扩展性和稳定性',
                'manager_id' => 4,
                'participant_count' => 6,
                'status' => 'on_hold',
                'priority' => 'urgent',
                'total_tasks' => 10,
                'created_at' => Carbon::now()->subDays(40),
                'expected_start_time' => Carbon::now()->subDays(30),
                'actual_start_time' => Carbon::now()->subDays(28),
                'expected_end_time' => Carbon::now()->addDays(20),
                'actual_end_time' => null,
                'updated_at' => Carbon::now()->subDays(3),
            ],
        ];
        DB::table('projects')->insert($projects);

        // 3. 创建任务数据
        $tasks = [
            // 电商平台开发项目的任务
            [
                'id' => 1,
                'title' => '用户注册登录模块',
                'description' => '实现用户注册、登录、密码重置等基础功能',
                'status' => 'completed',
                'priority' => 'high',
                'project_id' => 1,
                'created_at' => Carbon::now()->subDays(18),
                'expected_start_time' => Carbon::now()->subDays(18),
                'actual_start_time' => Carbon::now()->subDays(18),
                'expected_end_time' => Carbon::now()->subDays(12),
                'actual_end_time' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(10),
            ],
            [
                'id' => 2,
                'title' => '商品管理系统',
                'description' => '开发商品添加、编辑、删除、分类管理功能',
                'status' => 'in_progress',
                'priority' => 'high',
                'project_id' => 1,
                'created_at' => Carbon::now()->subDays(15),
                'expected_start_time' => Carbon::now()->subDays(10),
                'actual_start_time' => Carbon::now()->subDays(8),
                'expected_end_time' => Carbon::now()->addDays(5),
                'actual_end_time' => null,
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'id' => 3,
                'title' => '购物车功能',
                'description' => '实现购物车添加、删除、修改数量等功能',
                'status' => 'pending',
                'priority' => 'medium',
                'project_id' => 1,
                'created_at' => Carbon::now()->subDays(12),
                'expected_start_time' => Carbon::now()->addDays(3),
                'actual_start_time' => null,
                'expected_end_time' => Carbon::now()->addDays(10),
                'actual_end_time' => null,
                'updated_at' => Carbon::now()->subDays(12),
            ],
            [
                'id' => 4,
                'title' => '订单处理系统',
                'description' => '开发订单创建、支付、发货、退款等完整流程',
                'status' => 'pending',
                'priority' => 'high',
                'project_id' => 1,
                'created_at' => Carbon::now()->subDays(10),
                'expected_start_time' => Carbon::now()->addDays(8),
                'actual_start_time' => null,
                'expected_end_time' => Carbon::now()->addDays(20),
                'actual_end_time' => null,
                'updated_at' => Carbon::now()->subDays(10),
            ],
            // 移动应用开发项目的任务
            [
                'id' => 5,
                'title' => 'UI/UX设计',
                'description' => '设计移动应用的用户界面和用户体验',
                'status' => 'in_progress',
                'priority' => 'high',
                'project_id' => 2,
                'created_at' => Carbon::now()->subDays(10),
                'expected_start_time' => Carbon::now()->subDays(5),
                'actual_start_time' => Carbon::now()->subDays(3),
                'expected_end_time' => Carbon::now()->addDays(7),
                'actual_end_time' => null,
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'id' => 6,
                'title' => '前端开发',
                'description' => '使用React Native开发跨平台移动应用前端',
                'status' => 'pending',
                'priority' => 'medium',
                'project_id' => 2,
                'created_at' => Carbon::now()->subDays(8),
                'expected_start_time' => Carbon::now()->addDays(5),
                'actual_start_time' => null,
                'expected_end_time' => Carbon::now()->addDays(25),
                'actual_end_time' => null,
                'updated_at' => Carbon::now()->subDays(8),
            ],
            // 数据分析系统项目的任务（已完成）
            [
                'id' => 7,
                'title' => '数据收集模块',
                'description' => '开发数据收集和清洗模块',
                'status' => 'completed',
                'priority' => 'high',
                'project_id' => 3,
                'created_at' => Carbon::now()->subDays(50),
                'expected_start_time' => Carbon::now()->subDays(50),
                'actual_start_time' => Carbon::now()->subDays(48),
                'expected_end_time' => Carbon::now()->subDays(35),
                'actual_end_time' => Carbon::now()->subDays(33),
                'updated_at' => Carbon::now()->subDays(33),
            ],
            [
                'id' => 8,
                'title' => '数据可视化',
                'description' => '开发图表和仪表板功能',
                'status' => 'completed',
                'priority' => 'medium',
                'project_id' => 3,
                'created_at' => Carbon::now()->subDays(35),
                'expected_start_time' => Carbon::now()->subDays(30),
                'actual_start_time' => Carbon::now()->subDays(28),
                'expected_end_time' => Carbon::now()->subDays(15),
                'actual_end_time' => Carbon::now()->subDays(12),
                'updated_at' => Carbon::now()->subDays(12),
            ],
            // 云服务迁移项目的任务
            [
                'id' => 9,
                'title' => '环境搭建',
                'description' => '在云平台搭建开发和生产环境',
                'status' => 'completed',
                'priority' => 'urgent',
                'project_id' => 4,
                'created_at' => Carbon::now()->subDays(25),
                'expected_start_time' => Carbon::now()->subDays(25),
                'actual_start_time' => Carbon::now()->subDays(23),
                'expected_end_time' => Carbon::now()->subDays(18),
                'actual_end_time' => Carbon::now()->subDays(16),
                'updated_at' => Carbon::now()->subDays(16),
            ],
            [
                'id' => 10,
                'title' => '数据迁移',
                'description' => '将现有数据安全迁移到云数据库',
                'status' => 'in_progress',
                'priority' => 'urgent',
                'project_id' => 4,
                'created_at' => Carbon::now()->subDays(20),
                'expected_start_time' => Carbon::now()->subDays(15),
                'actual_start_time' => Carbon::now()->subDays(12),
                'expected_end_time' => Carbon::now()->addDays(5),
                'actual_end_time' => null,
                'updated_at' => Carbon::now()->subDays(1),
            ],
            // 独立任务（不属于任何项目）
            [
                'id' => 11,
                'title' => '代码审查',
                'description' => '对现有代码进行安全性和性能审查',
                'status' => 'pending',
                'priority' => 'low',
                'project_id' => null,
                'created_at' => Carbon::now()->subDays(5),
                'expected_start_time' => Carbon::now()->addDays(2),
                'actual_start_time' => null,
                'expected_end_time' => Carbon::now()->addDays(7),
                'actual_end_time' => null,
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'id' => 12,
                'title' => '技术文档编写',
                'description' => '编写项目技术文档和用户手册',
                'status' => 'in_progress',
                'priority' => 'medium',
                'project_id' => null,
                'created_at' => Carbon::now()->subDays(3),
                'expected_start_time' => Carbon::now()->subDays(2),
                'actual_start_time' => Carbon::now()->subDays(1),
                'expected_end_time' => Carbon::now()->addDays(10),
                'actual_end_time' => null,
                'updated_at' => Carbon::now(),
            ],
        ];
        DB::table('tasks')->insert($tasks);

        // 4. 创建用户通讯录数据
        $userContacts = [
            ['user_id' => 1, 'contact_user_id' => 2, 'contact_name' => '李四（前端开发）'],
            ['user_id' => 1, 'contact_user_id' => 3, 'contact_name' => '王五（后端开发）'],
            ['user_id' => 1, 'contact_user_id' => 4, 'contact_name' => '赵六（运维工程师）'],
            ['user_id' => 1, 'contact_user_id' => 5, 'contact_name' => '钱七（测试工程师）'],
            ['user_id' => 2, 'contact_user_id' => 1, 'contact_name' => '张三（项目经理）'],
            ['user_id' => 2, 'contact_user_id' => 3, 'contact_name' => '王五（后端）'],
            ['user_id' => 2, 'contact_user_id' => 6, 'contact_name' => '孙八（UI设计师）'],
            ['user_id' => 3, 'contact_user_id' => 1, 'contact_name' => '张三（PM）'],
            ['user_id' => 3, 'contact_user_id' => 2, 'contact_name' => '李四（前端）'],
            ['user_id' => 3, 'contact_user_id' => 7, 'contact_name' => '周九（数据分析师）'],
            ['user_id' => 4, 'contact_user_id' => 1, 'contact_name' => '张三'],
            ['user_id' => 4, 'contact_user_id' => 5, 'contact_name' => '钱七（测试）'],
            ['user_id' => 4, 'contact_user_id' => 8, 'contact_name' => '吴十（架构师）'],
        ];
        DB::table('user_contacts')->insert($userContacts);

        // 5. 创建用户项目角色数据
        $userProjectRoles = [
            // 电商平台开发项目团队
            [
                'user_id' => 1,
                'project_id' => 1,
                'role' => '项目经理',
                'is_manager' => true,
                'responsibilities' => '负责项目整体规划、进度管理和团队协调',
                'joined_at' => Carbon::now()->subDays(25),
            ],
            [
                'user_id' => 2,
                'project_id' => 1,
                'role' => '前端开发工程师',
                'is_manager' => false,
                'responsibilities' => '负责前端界面开发和用户体验优化',
                'joined_at' => Carbon::now()->subDays(20),
            ],
            [
                'user_id' => 3,
                'project_id' => 1,
                'role' => '后端开发工程师',
                'is_manager' => false,
                'responsibilities' => '负责后端API开发和数据库设计',
                'joined_at' => Carbon::now()->subDays(18),
            ],
            [
                'user_id' => 5,
                'project_id' => 1,
                'role' => '测试工程师',
                'is_manager' => false,
                'responsibilities' => '负责功能测试和质量保证',
                'joined_at' => Carbon::now()->subDays(15),
            ],
            [
                'user_id' => 6,
                'project_id' => 1,
                'role' => 'UI/UX设计师',
                'is_manager' => false,
                'responsibilities' => '负责界面设计和用户体验设计',
                'joined_at' => Carbon::now()->subDays(12),
            ],
            // 移动应用开发项目团队
            [
                'user_id' => 2,
                'project_id' => 2,
                'role' => '项目经理',
                'is_manager' => true,
                'responsibilities' => '负责移动应用项目管理和技术决策',
                'joined_at' => Carbon::now()->subDays(15),
            ],
            [
                'user_id' => 6,
                'project_id' => 2,
                'role' => 'UI/UX设计师',
                'is_manager' => false,
                'responsibilities' => '负责移动应用界面和交互设计',
                'joined_at' => Carbon::now()->subDays(12),
            ],
            [
                'user_id' => 7,
                'project_id' => 2,
                'role' => '移动端开发工程师',
                'is_manager' => false,
                'responsibilities' => '负责React Native应用开发',
                'joined_at' => Carbon::now()->subDays(10),
            ],
            [
                'user_id' => 3,
                'project_id' => 2,
                'role' => '后端开发工程师',
                'is_manager' => false,
                'responsibilities' => '负责移动应用后端API开发',
                'joined_at' => Carbon::now()->subDays(8),
            ],
            // 数据分析系统项目团队
            [
                'user_id' => 3,
                'project_id' => 3,
                'role' => '项目经理',
                'is_manager' => true,
                'responsibilities' => '负责数据分析项目管理和架构设计',
                'joined_at' => Carbon::now()->subDays(60),
            ],
            [
                'user_id' => 7,
                'project_id' => 3,
                'role' => '数据分析师',
                'is_manager' => false,
                'responsibilities' => '负责数据建模和分析算法设计',
                'joined_at' => Carbon::now()->subDays(55),
            ],
            [
                'user_id' => 8,
                'project_id' => 3,
                'role' => '全栈开发工程师',
                'is_manager' => false,
                'responsibilities' => '负责数据可视化和系统开发',
                'joined_at' => Carbon::now()->subDays(50),
            ],
            // 云服务迁移项目团队
            [
                'user_id' => 4,
                'project_id' => 4,
                'role' => '项目经理',
                'is_manager' => true,
                'responsibilities' => '负责云迁移项目规划和风险管理',
                'joined_at' => Carbon::now()->subDays(40),
            ],
            [
                'user_id' => 8,
                'project_id' => 4,
                'role' => '系统架构师',
                'is_manager' => false,
                'responsibilities' => '负责云架构设计和技术选型',
                'joined_at' => Carbon::now()->subDays(35),
            ],
            [
                'user_id' => 1,
                'project_id' => 4,
                'role' => '运维工程师',
                'is_manager' => false,
                'responsibilities' => '负责云环境部署和运维监控',
                'joined_at' => Carbon::now()->subDays(30),
            ],
            [
                'user_id' => 3,
                'project_id' => 4,
                'role' => '数据库工程师',
                'is_manager' => false,
                'responsibilities' => '负责数据库迁移和优化',
                'joined_at' => Carbon::now()->subDays(25),
            ],
            [
                'user_id' => 5,
                'project_id' => 4,
                'role' => '测试工程师',
                'is_manager' => false,
                'responsibilities' => '负责迁移测试和性能验证',
                'joined_at' => Carbon::now()->subDays(20),
            ],
            [
                'user_id' => 7,
                'project_id' => 4,
                'role' => '安全工程师',
                'is_manager' => false,
                'responsibilities' => '负责云安全配置和合规检查',
                'joined_at' => Carbon::now()->subDays(15),
            ],
        ];
        DB::table('user_project_roles')->insert($userProjectRoles);

        // 6. 创建用户任务分配数据
        $userTaskAssignments = [
            // 电商平台项目任务分配
            ['user_id' => 2, 'task_id' => 1, 'role' => '主要开发者', 'work_description' => '负责前端登录界面开发', 'assigned_at' => Carbon::now()->subDays(18)],
            ['user_id' => 3, 'task_id' => 1, 'role' => '主要开发者', 'work_description' => '负责后端认证API开发', 'assigned_at' => Carbon::now()->subDays(18)],
            ['user_id' => 5, 'task_id' => 1, 'role' => '测试人员', 'work_description' => '负责登录功能测试', 'assigned_at' => Carbon::now()->subDays(15)],
            
            ['user_id' => 2, 'task_id' => 2, 'role' => '主要开发者', 'work_description' => '负责商品管理前端界面', 'assigned_at' => Carbon::now()->subDays(15)],
            ['user_id' => 3, 'task_id' => 2, 'role' => '主要开发者', 'work_description' => '负责商品管理API开发', 'assigned_at' => Carbon::now()->subDays(15)],
            ['user_id' => 6, 'task_id' => 2, 'role' => '设计师', 'work_description' => '负责商品管理界面设计', 'assigned_at' => Carbon::now()->subDays(12)],
            
            ['user_id' => 2, 'task_id' => 3, 'role' => '主要开发者', 'work_description' => '负责购物车前端功能', 'assigned_at' => Carbon::now()->subDays(12)],
            ['user_id' => 3, 'task_id' => 3, 'role' => '支持开发者', 'work_description' => '负责购物车后端逻辑', 'assigned_at' => Carbon::now()->subDays(12)],
            
            ['user_id' => 3, 'task_id' => 4, 'role' => '主要开发者', 'work_description' => '负责订单系统架构设计', 'assigned_at' => Carbon::now()->subDays(10)],
            ['user_id' => 2, 'task_id' => 4, 'role' => '支持开发者', 'work_description' => '负责订单前端界面', 'assigned_at' => Carbon::now()->subDays(10)],
            
            // 移动应用项目任务分配
            ['user_id' => 6, 'task_id' => 5, 'role' => '主要设计师', 'work_description' => '负责移动应用UI设计', 'assigned_at' => Carbon::now()->subDays(10)],
            ['user_id' => 7, 'task_id' => 5, 'role' => '开发顾问', 'work_description' => '提供技术可行性建议', 'assigned_at' => Carbon::now()->subDays(8)],
            
            ['user_id' => 7, 'task_id' => 6, 'role' => '主要开发者', 'work_description' => '负责React Native开发', 'assigned_at' => Carbon::now()->subDays(8)],
            ['user_id' => 3, 'task_id' => 6, 'role' => '支持开发者', 'work_description' => '负责API接口对接', 'assigned_at' => Carbon::now()->subDays(8)],
            
            // 数据分析项目任务分配
            ['user_id' => 7, 'task_id' => 7, 'role' => '主要开发者', 'work_description' => '负责数据收集算法设计', 'assigned_at' => Carbon::now()->subDays(50)],
            ['user_id' => 8, 'task_id' => 7, 'role' => '支持开发者', 'work_description' => '负责数据清洗模块开发', 'assigned_at' => Carbon::now()->subDays(48)],
            
            ['user_id' => 8, 'task_id' => 8, 'role' => '主要开发者', 'work_description' => '负责图表组件开发', 'assigned_at' => Carbon::now()->subDays(35)],
            ['user_id' => 7, 'task_id' => 8, 'role' => '数据顾问', 'work_description' => '提供数据可视化建议', 'assigned_at' => Carbon::now()->subDays(30)],
            
            // 云服务迁移项目任务分配
            ['user_id' => 8, 'task_id' => 9, 'role' => '主要负责人', 'work_description' => '负责云架构设计和环境搭建', 'assigned_at' => Carbon::now()->subDays(25)],
            ['user_id' => 1, 'task_id' => 9, 'role' => '运维支持', 'work_description' => '负责环境配置和监控设置', 'assigned_at' => Carbon::now()->subDays(23)],
            
            ['user_id' => 3, 'task_id' => 10, 'role' => '主要负责人', 'work_description' => '负责数据库迁移策略制定', 'assigned_at' => Carbon::now()->subDays(20)],
            ['user_id' => 8, 'task_id' => 10, 'role' => '技术支持', 'work_description' => '提供云数据库技术支持', 'assigned_at' => Carbon::now()->subDays(18)],
            ['user_id' => 5, 'task_id' => 10, 'role' => '测试验证', 'work_description' => '负责数据迁移测试验证', 'assigned_at' => Carbon::now()->subDays(15)],
            
            // 独立任务分配
            ['user_id' => 8, 'task_id' => 11, 'role' => '主要审查者', 'work_description' => '负责代码安全性审查', 'assigned_at' => Carbon::now()->subDays(5)],
            ['user_id' => 1, 'task_id' => 11, 'role' => '协助审查者', 'work_description' => '负责代码规范性检查', 'assigned_at' => Carbon::now()->subDays(5)],
            
            ['user_id' => 1, 'task_id' => 12, 'role' => '主要编写者', 'work_description' => '负责项目管理文档编写', 'assigned_at' => Carbon::now()->subDays(3)],
            ['user_id' => 8, 'task_id' => 12, 'role' => '技术审核者', 'work_description' => '负责技术文档审核', 'assigned_at' => Carbon::now()->subDays(2)],
        ];
        DB::table('user_task_assignments')->insert($userTaskAssignments);

        // 7. 创建项目任务关系数据
        $projectTaskRelations = [
            ['project_id' => 1, 'task_id' => 1, 'is_critical' => true, 'completion_weight' => 15],
            ['project_id' => 1, 'task_id' => 2, 'is_critical' => true, 'completion_weight' => 20],
            ['project_id' => 1, 'task_id' => 3, 'is_critical' => false, 'completion_weight' => 15],
            ['project_id' => 1, 'task_id' => 4, 'is_critical' => true, 'completion_weight' => 25],
            
            ['project_id' => 2, 'task_id' => 5, 'is_critical' => true, 'completion_weight' => 30],
            ['project_id' => 2, 'task_id' => 6, 'is_critical' => true, 'completion_weight' => 40],
            
            ['project_id' => 3, 'task_id' => 7, 'is_critical' => true, 'completion_weight' => 50],
            ['project_id' => 3, 'task_id' => 8, 'is_critical' => false, 'completion_weight' => 30],
            
            ['project_id' => 4, 'task_id' => 9, 'is_critical' => true, 'completion_weight' => 20],
            ['project_id' => 4, 'task_id' => 10, 'is_critical' => true, 'completion_weight' => 30],
        ];
        DB::table('project_task_relations')->insert($projectTaskRelations);

        // 8. 创建任务依赖关系数据
        $taskDependencies = [
            // 电商平台项目内的任务依赖
            ['task_id' => 2, 'depends_on_task_id' => 1, 'dependency_type' => 'finish_to_start', 'lag_days' => 1],
            ['task_id' => 3, 'depends_on_task_id' => 2, 'dependency_type' => 'start_to_start', 'lag_days' => 5],
            ['task_id' => 4, 'depends_on_task_id' => 3, 'dependency_type' => 'finish_to_start', 'lag_days' => 2],
            
            // 移动应用项目内的任务依赖
            ['task_id' => 6, 'depends_on_task_id' => 5, 'dependency_type' => 'finish_to_start', 'lag_days' => 0],
            
            // 数据分析项目内的任务依赖
            ['task_id' => 8, 'depends_on_task_id' => 7, 'dependency_type' => 'finish_to_start', 'lag_days' => 3],
            
            // 云服务迁移项目内的任务依赖
            ['task_id' => 10, 'depends_on_task_id' => 9, 'dependency_type' => 'finish_to_start', 'lag_days' => 2],
            
            // 跨项目任务依赖（代码审查依赖于电商平台的商品管理完成）
            ['task_id' => 11, 'depends_on_task_id' => 2, 'dependency_type' => 'finish_to_start', 'lag_days' => 1],
        ];
        DB::table('task_dependencies')->insert($taskDependencies);

        $this->command->info('数据库种子数据已成功创建！');
        $this->command->info('创建了8个用户、4个项目、12个任务以及完整的关系数据。');
    }
}
