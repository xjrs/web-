<template>
  <div class="task-page">
    <!-- Header Section -->
    <div class="page-header">
      <div class="header-content">
        <div class="title-section">
          <h1 class="page-title">
            <span class="title-icon">✅</span>
            任务管理
          </h1>
          <p class="page-subtitle">高效管理您的工作任务</p>
        </div>
        <div class="header-actions">
          <button class="btn btn-primary" @click="showAddModal = true">
            <span class="btn-icon">➕</span>
            新建任务
          </button>
          <button class="btn btn-secondary" @click="refreshTasks">
            <span class="btn-icon">🔄</span>
            刷新
          </button>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-section">
      <div class="stats-grid">
        <div class="stat-card total">
          <div class="stat-icon">📋</div>
          <div class="stat-content">
            <div class="stat-number">{{ taskStats.total }}</div>
            <div class="stat-label">总任务</div>
          </div>
        </div>
        <div class="stat-card pending">
          <div class="stat-icon">⏳</div>
          <div class="stat-content">
            <div class="stat-number">{{ taskStats.pending }}</div>
            <div class="stat-label">待办</div>
          </div>
        </div>
        <div class="stat-card progress">
          <div class="stat-icon">🔄</div>
          <div class="stat-content">
            <div class="stat-number">{{ taskStats.inProgress }}</div>
            <div class="stat-label">进行中</div>
          </div>
        </div>
        <div class="stat-card completed">
          <div class="stat-icon">✅</div>
          <div class="stat-content">
            <div class="stat-number">{{ taskStats.completed }}</div>
            <div class="stat-label">已完成</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters Section -->
    <div class="filters-section">
      <div class="filters-content">
        <div class="filter-group">
          <label class="filter-label">状态筛选：</label>
          <select v-model="filterStatus" @change="filterTasks" class="filter-select">
            <option value="">全部</option>
            <option value="待办">待办</option>
            <option value="进行中">进行中</option>
            <option value="已完成">已完成</option>
            <option value="已取消">已取消</option>
          </select>
        </div>
        <div class="filter-group">
          <label class="filter-label">优先级：</label>
          <select v-model="filterPriority" @change="filterTasks" class="filter-select">
            <option value="">全部</option>
            <option value="高">高</option>
            <option value="中">中</option>
            <option value="低">低</option>
          </select>
        </div>
        <div class="filter-group">
          <label class="filter-label">搜索：</label>
          <div class="search-input-wrapper">
            <input 
              type="text"
              v-model="searchText"
              @input="filterTasks"
              placeholder="输入任务名称搜索..."
              class="search-input"
            />
            <span class="search-icon">🔍</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Tasks Cards -->
    <div class="cards-section">
      <div class="cards-container">
        <div class="cards-header">
          <div class="cards-title">任务列表</div>
          <div class="cards-actions">
            <span class="task-count">共 {{ filteredTasks.length }} 个任务</span>
          </div>
        </div>
        
        <div class="cards-grid">
          <div 
            v-for="task in filteredTasks" 
            :key="task.id" 
            class="task-card"
            @click="showTaskDetail(task)"
          >
            <div class="task-card-header">
              <div class="task-title">{{ task.name }}</div>
              <div class="task-actions">
                <button 
                  class="btn-action btn-edit"
                  @click.stop="editTask(task)"
                  title="编辑"
                >
                  ✏️
                </button>
                <button 
                  class="btn-action btn-delete"
                  @click.stop="deleteTask(task)"
                  title="删除"
                >
                  🗑️
                </button>
              </div>
            </div>
            
            <div class="task-card-body">
              <div class="task-meta">
                <span class="task-status" :class="getStatusClass(task.status)">
                  {{ task.status }}
                </span>
                <span class="task-priority" :class="getPriorityClass(task.priority)">
                  {{ task.priority }}
                </span>
              </div>
              
              <div class="task-manager">
                <span class="manager-label">负责人：</span>
                <span class="manager-name">{{ task.assignee || '未分配' }}</span>
              </div>
              
              <div class="task-due-date">
                <span class="due-label">截止日期：</span>
                <span class="due-date">{{ task.dueDate || '未设置' }}</span>
              </div>
              
              <div class="task-progress">
                <span class="progress-label">进度：</span>
                <div class="progress-bar">
                  <div class="progress-fill" :style="{ width: task.progress + '%' }"></div>
                </div>
                <span class="progress-text">{{ task.progress }}%</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Task Detail Modal -->
    <div class="modal-overlay" v-if="showDetailModal" @click="closeDetailModal">
      <div class="modal-content task-detail-modal" @click.stop>
        <div class="modal-header">
          <h3>任务详情</h3>
          <button class="modal-close" @click="closeDetailModal">×</button>
        </div>
        <div class="modal-body" v-if="selectedTask">
          <div class="detail-section">
            <h4>基本信息</h4>
            <div class="detail-item">
              <label>任务标题：</label>
              <span>{{ selectedTask.name }}</span>
            </div>
            <div class="detail-item">
              <label>任务描述：</label>
              <p>{{ selectedTask.description || '暂无描述' }}</p>
            </div>
          </div>
          
          <div class="detail-section">
            <h4>时间信息</h4>
            <div class="detail-item">
              <label>创建时间：</label>
              <span>{{ formatDate(selectedTask.created_at) }}</span>
            </div>
            <div class="detail-item">
              <label>预计开始时间：</label>
              <span>{{ formatDate(selectedTask.expected_start_time) }}</span>
            </div>
            <div class="detail-item">
              <label>实际开始时间：</label>
              <span>{{ formatDate(selectedTask.actual_start_time) || '未开始' }}</span>
            </div>
            <div class="detail-item">
              <label>预计结束时间：</label>
              <span>{{ formatDate(selectedTask.expected_end_time) }}</span>
            </div>
            <div class="detail-item">
              <label>实际结束时间：</label>
              <span>{{ formatDate(selectedTask.actual_end_time) || '未完成' }}</span>
            </div>
          </div>
          
          <div class="detail-section">
            <h4>项目信息</h4>
            <div class="detail-item">
              <label>是否关键任务：</label>
              <span class="critical-badge" :class="{ 'is-critical': selectedTask.is_critical }">
                {{ selectedTask.is_critical ? '是' : '否' }}
              </span>
            </div>
            <div class="detail-item">
              <label>完成权重：</label>
              <span>{{ selectedTask.completion_weight || 1.0 }}</span>
            </div>
          </div>
          
          <div class="detail-section">
            <h4>任务成员</h4>
            <div class="members-list">
              <div 
                v-for="member in selectedTask.members" 
                :key="member.id"
                class="member-item"
              >
                <div class="member-info">
                  <div class="member-name">{{ member.name }}</div>
                  <div class="member-email">{{ member.email }}</div>
                  <div class="member-role">{{ member.role }}</div>
                  <div class="member-work">{{ member.work_description }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" @click="closeDetailModal">关闭</button>
        </div>
      </div>
    </div>

    <!-- Bulk Actions -->
    <div class="bulk-actions" v-if="selectedTasks.length > 0">
      <div class="bulk-content">
        <span class="bulk-info">已选择 {{ selectedTasks.length }} 个任务</span>
        <div class="bulk-buttons">
          <button class="btn btn-secondary" @click="bulkUpdateStatus">批量更新状态</button>
          <button class="btn btn-danger" @click="bulkDelete">批量删除</button>
        </div>
      </div>
    </div>

    <!-- Add Task Modal -->
    <div class="modal-overlay" v-if="showAddModal" @click="closeModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>新建任务</h3>
          <button class="modal-close" @click="showAddModal = false">×</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>任务名称</label>
            <input 
              type="text" 
              v-model="newTask.name" 
              placeholder="请输入任务名称"
              class="form-input"
            />
          </div>
          <div class="form-group">
            <label>状态</label>
            <select v-model="newTask.status" class="form-select">
              <option value="待办">待办</option>
              <option value="进行中">进行中</option>
              <option value="已完成">已完成</option>
            </select>
          </div>
          <div class="form-group">
            <label>优先级</label>
            <select v-model="newTask.priority" class="form-select">
              <option value="高">高</option>
              <option value="中">中</option>
              <option value="低">低</option>
            </select>
          </div>
          <div class="form-group">
            <label>负责人</label>
            <input 
              type="text" 
              v-model="newTask.assignee" 
              placeholder="请输入负责人"
              class="form-input"
            />
          </div>
          <div class="form-group">
            <label>截止日期</label>
            <input 
              type="date" 
              v-model="newTask.dueDate"
              class="form-input"
            />
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" @click="showAddModal = false">取消</button>
          <button class="btn btn-primary" @click="addTask">确认添加</button>
        </div>
      </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" v-if="loading">
      <div class="loading-spinner">
        <div class="spinner"></div>
        <p>加载中...</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TaskPage',
  data() {
    return {
      loading: false,
      showAddModal: false,
      showDetailModal: false,
      selectedTask: null,
      tasks: [
        { id: 1, name: '完成项目文档', status: '进行中', priority: '高', assignee: '张三', dueDate: '2024-01-15', progress: 60 },
        { id: 2, name: '代码审查', status: '待办', priority: '中', assignee: '李四', dueDate: '2024-01-20', progress: 0 },
        { id: 3, name: '测试用例编写', status: '已完成', priority: '高', assignee: '王五', dueDate: '2024-01-10', progress: 100 },
        { id: 4, name: 'UI设计优化', status: '待办', priority: '低', assignee: '赵六', dueDate: '2024-01-25', progress: 20 },
        { id: 5, name: '数据库设计', status: '进行中', priority: '高', assignee: '孙七', dueDate: '2024-01-18', progress: 45 }
      ],
      filteredTasks: [],
      selectedTasks: [],
      filterStatus: '',
      filterPriority: '',
      searchText: '',
      newTask: {
        name: '',
        status: '待办',
        priority: '中',
        assignee: '',
        dueDate: '',
        progress: 0
      }
    }
  },
  computed: {
    taskStats() {
      return {
        total: this.tasks.length,
        pending: this.tasks.filter(t => t.status === '待办').length,
        inProgress: this.tasks.filter(t => t.status === '进行中').length,
        completed: this.tasks.filter(t => t.status === '已完成').length
      }
    },
    isAllSelected() {
      return this.filteredTasks.length > 0 && this.selectedTasks.length === this.filteredTasks.length;
    }
  },
  methods: {
    filterTasks() {
      let filtered = [...this.tasks];
      
      if (this.filterStatus) {
        filtered = filtered.filter(task => task.status === this.filterStatus);
      }
      
      if (this.filterPriority) {
        filtered = filtered.filter(task => task.priority === this.filterPriority);
      }
      
      if (this.searchText) {
        filtered = filtered.filter(task => 
          task.name.toLowerCase().includes(this.searchText.toLowerCase()) ||
          task.assignee.toLowerCase().includes(this.searchText.toLowerCase())
        );
      }
      
      this.filteredTasks = filtered;
    },
    
    async loadTasks() {
      this.loading = true;
      try {
        // 模拟API调用
        setTimeout(() => {
          this.filterTasks();
          this.loading = false;
        }, 500);
      } catch(err) {
        console.error('加载任务失败', err);
        this.loading = false;
      }
    },
    
    async updateTask(task) {
      try {
        // 模拟API调用
        console.log('更新任务:', task);
        // 实际项目中这里应该是API调用
        // await fetch(`/api/tasks/${task.id}`, { method: 'PUT', ... });
      } catch(err) {
        console.error('更新失败:', err);
      }
    },
    
    async addTask() {
      if (!this.newTask.name.trim()) {
        alert('请输入任务名称');
        return;
      }
      
      const newTaskId = Math.max(...this.tasks.map(t => t.id)) + 1;
      const taskToAdd = {
        ...this.newTask,
        id: newTaskId
      };
      
      this.tasks.push(taskToAdd);
      this.filterTasks();
      this.resetNewTask();
      this.showAddModal = false;
    },
    
    async deleteTask(task) {
      if (confirm('确定要删除这个任务吗？')) {
        this.tasks = this.tasks.filter(t => t.id !== task.id);
        this.selectedTasks = this.selectedTasks.filter(id => id !== task.id);
        this.filterTasks();
      }
    },
    
    editTask(task) {
      console.log('编辑任务:', task);
    },
    
    bulkDelete() {
      if (confirm(`确定要删除选中的 ${this.selectedTasks.length} 个任务吗？`)) {
        this.tasks = this.tasks.filter(task => !this.selectedTasks.includes(task.id));
        this.selectedTasks = [];
        this.filterTasks();
      }
    },
    
    bulkUpdateStatus() {
      const newStatus = prompt('请输入新状态（待办/进行中/已完成）：');
      if (newStatus && ['待办', '进行中', '已完成'].includes(newStatus)) {
        this.tasks.forEach(task => {
          if (this.selectedTasks.includes(task.id)) {
            task.status = newStatus;
          }
        });
        this.filterTasks();
      }
    },
    
    toggleSelectAll() {
      if (this.isAllSelected) {
        this.selectedTasks = [];
      } else {
        this.selectedTasks = this.filteredTasks.map(task => task.id);
      }
    },
    
    refreshTasks() {
      this.loadTasks();
    },
    
    resetNewTask() {
      this.newTask = {
        name: '',
        status: '待办',
        priority: '中',
        assignee: '',
        dueDate: '',
        progress: 0
      };
    },
    
    closeModal() {
      this.showAddModal = false;
    },
    
    getStatusClass(status) {
      const classes = {
        '待办': 'status-pending',
        '进行中': 'status-progress',
        '已完成': 'status-completed',
        '已取消': 'status-cancelled'
      };
      return classes[status] || '';
    },
    
    getPriorityClass(priority) {
      const classes = {
        '高': 'priority-high',
        '中': 'priority-medium',
        '低': 'priority-low'
      };
      return classes[priority] || '';
    },

    showTaskDetail(task) {
      this.selectedTask = task;
      this.showDetailModal = true;
    },

    closeDetailModal() {
      this.showDetailModal = false;
      this.selectedTask = null;
    },

    formatDate(dateString) {
      if (!dateString) return '';
      const date = new Date(dateString);
      return date.toLocaleDateString('zh-CN');
    }
  },
  
  mounted() {
    this.loadTasks();
  },
  
  watch: {
    tasks: {
      handler() {
        this.filterTasks();
      },
      deep: true
    }
  }
}
</script>

<style scoped>
* {
  box-sizing: border-box;
}

.task-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  padding: 2rem;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Header Section */
.page-header {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border-radius: 16px;
  padding: 2rem;
  margin-bottom: 2rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.title-section {
  flex: 1;
}

.page-title {
  display: flex;
  align-items: center;
  gap: 1rem;
  font-size: 2.5rem;
  font-weight: 700;
  color: #1e293b;
  margin: 0;
}

.title-icon {
  font-size: 2.5rem;
}

.page-subtitle {
  color: #64748b;
  font-size: 1.1rem;
  margin: 0.5rem 0 0 0;
}

.header-actions {
  display: flex;
  gap: 1rem;
}

/* Buttons */
.btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 8px;
  font-size: 0.9rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
}

.btn-primary {
  background: #6366f1;
  color: white;
}

.btn-primary:hover {
  background: #5855eb;
  transform: translateY(-1px);
}

.btn-secondary {
  background: #f1f5f9;
  color: #64748b;
  border: 1px solid #e2e8f0;
}

.btn-secondary:hover {
  background: #e2e8f0;
}

.btn-danger {
  background: #ef4444;
  color: white;
}

.btn-danger:hover {
  background: #dc2626;
}

.btn-icon {
  font-size: 1rem;
}

/* Stats Section */
.stats-section {
  margin-bottom: 2rem;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
}

.stat-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border-radius: 16px;
  padding: 2rem;
  display: flex;
  align-items: center;
  gap: 1.5rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  transition: transform 0.2s ease;
}

.stat-card:hover {
  transform: translateY(-2px);
}

.stat-icon {
  font-size: 3rem;
  opacity: 0.8;
}

.stat-number {
  font-size: 2.5rem;
  font-weight: 700;
  color: #1e293b;
  line-height: 1;
}

.stat-label {
  color: #64748b;
  font-size: 1rem;
  margin-top: 0.5rem;
}

.stat-card.total { border-left: 4px solid #6366f1; }
.stat-card.pending { border-left: 4px solid #f59e0b; }
.stat-card.progress { border-left: 4px solid #3b82f6; }
.stat-card.completed { border-left: 4px solid #10b981; }

/* Filters Section */
.filters-section {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border-radius: 16px;
  padding: 1.5rem 2rem;
  margin-bottom: 2rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.filters-content {
  display: flex;
  gap: 2rem;
  align-items: center;
  flex-wrap: wrap;
}

.filter-group {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.filter-label {
  font-weight: 600;
  color: #374151;
  white-space: nowrap;
}

.filter-select {
  padding: 0.5rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  background: white;
  font-size: 0.9rem;
  min-width: 120px;
}

.search-input-wrapper {
  position: relative;
}

.search-input {
  padding: 0.5rem 1rem 0.5rem 2.5rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  background: white;
  font-size: 0.9rem;
  min-width: 200px;
}

.search-icon {
  position: absolute;
  left: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  color: #9ca3af;
}

/* Table Section */
.table-section {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border-radius: 16px;
  padding: 2rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  margin-bottom: 2rem;
}

.table-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.table-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1e293b;
}

.task-count {
  color: #64748b;
  font-size: 0.9rem;
}

.table-wrapper {
  overflow-x: auto;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
}

.modern-table {
  width: 100%;
  border-collapse: collapse;
  background: white;
}

.modern-table th {
  background: #f8fafc;
  padding: 1rem;
  text-align: left;
  font-weight: 600;
  color: #374151;
  border-bottom: 1px solid #e2e8f0;
}

.modern-table td {
  padding: 1rem;
  border-bottom: 1px solid #f1f5f9;
}

.table-row:hover {
  background: #f8fafc;
}

/* Table Cell Styles */
.task-id {
  background: #f0f9ff;
  color: #0369a1;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-weight: 600;
  font-size: 0.8rem;
}

.task-name-input {
  width: 100%;
  border: none;
  background: transparent;
  font-size: 0.9rem;
  padding: 0.25rem;
}

.task-name-input:focus {
  outline: 1px solid #6366f1;
  border-radius: 4px;
}

.status-select {
  border: none;
  background: transparent;
  font-size: 0.9rem;
  padding: 0.25rem;
  border-radius: 4px;
}

.status-pending { color: #f59e0b; }
.status-progress { color: #3b82f6; }
.status-completed { color: #10b981; }
.status-cancelled { color: #ef4444; }

.priority-tag {
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
}

.priority-high {
  background: #fee2e2;
  color: #dc2626;
}

.priority-medium {
  background: #fef3c7;
  color: #d97706;
}

.priority-low {
  background: #ecfdf5;
  color: #16a34a;
}

.assignee-input {
  width: 100%;
  border: none;
  background: transparent;
  font-size: 0.9rem;
  padding: 0.25rem;
}

.assignee-input:focus {
  outline: 1px solid #6366f1;
  border-radius: 4px;
}

.date-input {
  border: none;
  background: transparent;
  font-size: 0.9rem;
  padding: 0.25rem;
}

.progress-cell {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.progress-bar {
  flex: 1;
  height: 8px;
  background: #f1f5f9;
  border-radius: 4px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #6366f1, #8b5cf6);
  transition: width 0.3s ease;
}

.progress-text {
  font-size: 0.8rem;
  font-weight: 600;
  color: #374151;
  min-width: 35px;
}

.action-buttons {
  display: flex;
  gap: 0.5rem;
}

.btn-action {
  background: none;
  border: none;
  font-size: 1.2rem;
  cursor: pointer;
  padding: 0.25rem;
  border-radius: 4px;
  transition: background 0.2s ease;
}

.btn-action:hover {
  background: #f1f5f9;
}

.checkbox {
  width: 1.2rem;
  height: 1.2rem;
  cursor: pointer;
}

/* Bulk Actions */
.bulk-actions {
  position: fixed;
  bottom: 2rem;
  left: 50%;
  transform: translateX(-50%);
  background: rgba(255, 255, 255, 0.98);
  backdrop-filter: blur(10px);
  border-radius: 50px;
  padding: 1rem 2rem;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
  z-index: 1000;
}

.bulk-content {
  display: flex;
  align-items: center;
  gap: 2rem;
}

.bulk-info {
  font-weight: 600;
  color: #374151;
}

.bulk-buttons {
  display: flex;
  gap: 1rem;
}

/* Modal */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
}

.modal-content {
  background: white;
  border-radius: 16px;
  width: 100%;
  max-width: 500px;
  max-height: 80vh;
  overflow-y: auto;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 2rem;
  border-bottom: 1px solid #e2e8f0;
}

/* 补充的样式部分 */

.modal-header h3 {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 600;
  color: #1e293b;
}

.modal-close {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: #64748b;
  padding: 0.25rem;
  border-radius: 4px;
  transition: background 0.2s ease;
}

.modal-close:hover {
  background: #f1f5f9;
}

.modal-body {
  padding: 2rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: #374151;
}

.form-input,
.form-select {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 0.9rem;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.form-input:focus,
.form-select:focus {
  outline: none;
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  padding: 1.5rem 2rem;
  border-top: 1px solid #e2e8f0;
  background: #f8fafc;
  border-radius: 0 0 16px 16px;
}

/* Loading Overlay */
.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(5px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 3000;
}

.loading-spinner {
  text-align: center;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #f3f4f6;
  border-top: 4px solid #6366f1;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.loading-spinner p {
  color: #64748b;
  font-weight: 500;
  margin: 0;
}

/* 响应式设计 */
@media (max-width: 1024px) {
  .task-page {
    padding: 1rem;
  }
  
  .header-content {
    flex-direction: column;
    gap: 1.5rem;
    align-items: flex-start;
  }
  
  .stats-grid {
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  }
  
  .filters-content {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .filter-group {
    width: 100%;
  }
  
  .search-input {
    min-width: 100%;
  }
}

@media (max-width: 768px) {
  .page-title {
    font-size: 2rem;
  }
  
  .stat-card {
    padding: 1.5rem;
  }
  
  .stat-number {
    font-size: 2rem;
  }
  
  .stat-icon {
    font-size: 2.5rem;
  }
  
  .table-wrapper {
    font-size: 0.8rem;
  }
  
  .modern-table th,
  .modern-table td {
    padding: 0.75rem 0.5rem;
  }
  
  .bulk-actions {
    left: 1rem;
    right: 1rem;
    transform: none;
    border-radius: 12px;
  }
  
  .bulk-content {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
  
  .modal-content {
    margin: 1rem;
    max-width: calc(100% - 2rem);
  }
}

@media (max-width: 480px) {
  .header-actions {
    flex-direction: column;
    width: 100%;
  }
  
  .btn {
    justify-content: center;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .table-section {
    padding: 1rem;
  }
  
  .modal-body {
    padding: 1.5rem;
  }
  
  .modal-footer {
    padding: 1rem 1.5rem;
  }
}

/* 滚动条样式 */
.table-wrapper::-webkit-scrollbar {
  height: 8px;
}

.table-wrapper::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 4px;
}

.table-wrapper::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}

.table-wrapper::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Cards Section */
.cards-section {
  margin-bottom: 2rem;
}

.cards-container {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border-radius: 16px;
  padding: 2rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.cards-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #e2e8f0;
}

.cards-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
}

.task-count {
  color: #64748b;
  font-size: 0.9rem;
}

.cards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 1.5rem;
}

.task-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  border: 1px solid #e2e8f0;
  transition: all 0.2s ease;
  cursor: pointer;
}

.task-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  border-color: #6366f1;
}

.task-card-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
}

.task-title {
  font-size: 1.1rem;
  font-weight: 600;
  color: #1e293b;
  line-height: 1.4;
  flex: 1;
  margin-right: 1rem;
}

.task-actions {
  display: flex;
  gap: 0.5rem;
}

.btn-action {
  background: none;
  border: none;
  padding: 0.5rem;
  border-radius: 6px;
  cursor: pointer;
  font-size: 1rem;
  transition: background 0.2s ease;
}

.btn-action:hover {
  background: #f1f5f9;
}

.task-card-body {
  space-y: 1rem;
}

.task-meta {
  display: flex;
  gap: 0.75rem;
  margin-bottom: 1rem;
}

.task-status, .task-priority {
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
}

.task-manager, .task-due-date {
  display: flex;
  align-items: center;
  margin-bottom: 0.75rem;
  font-size: 0.9rem;
}

.manager-label, .due-label, .progress-label {
  color: #64748b;
  margin-right: 0.5rem;
  font-weight: 500;
}

.manager-name, .due-date {
  color: #1e293b;
  font-weight: 600;
}

.task-progress {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 0.9rem;
}

.progress-bar {
  flex: 1;
  height: 6px;
  background: #e2e8f0;
  border-radius: 3px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #6366f1, #8b5cf6);
  border-radius: 3px;
  transition: width 0.3s ease;
}

.progress-text {
  color: #64748b;
  font-weight: 600;
  min-width: 40px;
  text-align: right;
}

/* 动画效果 */
.stat-card,
.modal-content,
.task-card {
  animation: fadeInUp 0.3s ease-out;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.bulk-actions {
  animation: slideInUp 0.3s ease-out;
}

@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateX(-50%) translateY(100%);
  }
  to {
    opacity: 1;
    transform: translateX(-50%) translateY(0);
  }
}

/* 打印样式 */
@media print {
  .page-header,
  .filters-section,
  .bulk-actions,
  .modal-overlay,
  .loading-overlay {
    display: none;
  }
  
  .task-page {
    background: white;
    padding: 0;
  }
  
  .table-section {
    box-shadow: none;
    border: 1px solid #000;
  }
  
  .modern-table {
    font-size: 12px;
  }
  
  .action-buttons {
    display: none;
  }
}

</style>