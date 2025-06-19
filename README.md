# TaskFlow - 智能工作台

一个基于 Laravel + Vue.js 的现代化任务管理系统，提供高效的任务管理、日程安排和数据分析功能。

## 🚀 技术栈

### 后端
- **框架**: Laravel 9.x
- **语言**: PHP 8.0+
- **数据库**: MySQL
- **API**: RESTful API

### 前端
- **框架**: Vue 3 + Vue Router 4
- **UI库**: Element Plus
- **图表**: ECharts
- **日历**: FullCalendar
- **数据表格**: DHTMLX Suite
- **构建工具**: Vite

## 📁 项目结构

```
web期末/
├── task-scheduler/          # Laravel 后端
│   ├── app/
│   │   ├── Http/Controllers/
│   │   │   └── TaskController.php
│   │   └── Models/
│   │       └── Task.php
│   ├── database/migrations/
│   │   └── create_tasks_table.php
│   ├── routes/
│   │   └── api.php
│   └── resources/vue-src/vue-src/task-frontend/  # Vue.js 前端
│       ├── src/
│       │   ├── views/
│       │   │   ├── TaskPage.vue
│       │   │   ├── CalendarPage.vue
│       │   │   └── StatisticsPage.vue
│       │   └── components/
│       │       ├── TaskGrid.vue
│       │       └── ChartDashboard.vue
│       └── package.json
└── README.md
```

## 🛠️ 安装与配置

### 环境要求
- PHP >= 8.0
- Composer
- Node.js >= 16
- MySQL >= 5.7

### 后端安装

1. **进入Laravel项目目录**
   ```bash
   cd task-scheduler
   ```

2. **安装PHP依赖**
   ```bash
   composer install
   ```

3. **配置环境变量**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **配置数据库**
   编辑 `.env` 文件，设置数据库连接信息：
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=taskflow
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **运行数据库迁移**
   ```bash
   php artisan migrate
   ```

6. **启动Laravel服务**
   ```bash
   php artisan serve
   ```

### 前端安装

1. **进入前端项目目录**
   ```bash
   cd resources/vue-src/vue-src/task-frontend
   ```

2. **安装Node.js依赖**
   ```bash
   npm install
   ```

3. **启动开发服务器**
   ```bash
   npm run dev
   ```

## 📖 使用说明

### 功能模块

1. **任务管理** (`/tasks`)
   - 创建、编辑、删除任务
   - 任务状态管理（待办、进行中、已完成）
   - 批量操作和筛选

2. **日程安排** (`/calendar`)
   - 日历视图显示任务
   - 拖拽调整任务时间
   - 日程冲突检测

3. **统计分析** (`/statistics`)
   - 任务完成率趋势
   - 状态分布饼图
   - 优先级分布柱状图

4. **项目总览** (`/project`)
   - 项目进度概览
   - 团队成员任务分配

### API接口

- `GET /api/tasks` - 获取所有任务
- `POST /api/tasks` - 创建新任务
- `GET /api/tasks/{id}` - 获取单个任务
- `PUT /api/tasks/{id}` - 更新任务
- `DELETE /api/tasks/{id}` - 删除任务

## 🔧 开发指南

### 添加新功能
1. 在 `app/Http/Controllers/` 创建控制器
2. 在 `routes/api.php` 添加路由
3. 在 `app/Models/` 创建模型
4. 在 `database/migrations/` 创建迁移文件
5. 在前端 `src/views/` 创建页面组件

### 代码规范
- 使用 ESLint 和 Prettier 保持代码风格一致
- 遵循 Laravel 和 Vue.js 最佳实践
- 编写清晰的注释和文档

## 📝 更新日志

### v1.0.0 (2024-01-XX)
- ✅ 基础任务管理功能
- ✅ 日历日程安排
- ✅ 数据统计图表
- ✅ 响应式UI设计
- ✅ RESTful API接口

## 🤝 贡献指南

1. Fork 项目
2. 创建功能分支 (`git checkout -b feature/AmazingFeature`)
3. 提交更改 (`git commit -m 'Add some AmazingFeature'`)
4. 推送到分支 (`git push origin feature/AmazingFeature`)
5. 打开 Pull Request

## 📄 许可证

本项目采用 MIT 许可证 - 查看 [LICENSE](LICENSE) 文件了解详情

## 📞 联系方式

如有问题或建议，请通过以下方式联系：
- 项目Issues: [GitHub Issues](https://github.com/your-username/taskflow/issues)
- 邮箱: your-email@example.com

---

**TaskFlow** - 让工作更高效，让管理更智能！ 🎯 