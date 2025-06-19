<template>
  <div class="project-page">
    <!-- Header Section -->
    <div class="page-header">
      <div class="header-content">
        <div class="title-section">
          <h1 class="page-title">
            <span class="title-icon">📊</span>
            项目总览
          </h1>
          <p class="page-subtitle">管理和跟踪所有项目进度</p>
        </div>
        <div class="header-actions">
          <button class="btn btn-primary" @click="showCreateModal = true">
            <span class="btn-icon">➕</span>
            新建项目
          </button>
          <button class="btn btn-secondary" @click="refreshProjects">
            <span class="btn-icon">🔄</span>
            刷新
          </button>
        </div>
      </div>
    </div>

    <!-- Stats Overview -->
    <div class="stats-section">
      <div class="stats-grid">
        <div class="stat-card total">
          <div class="stat-icon">📁</div>
          <div class="stat-content">
            <div class="stat-number">{{ projectStats.total }}</div>
            <div class="stat-label">总项目数</div>
          </div>
        </div>
        <div class="stat-card active">
          <div class="stat-icon">🚀</div>
          <div class="stat-content">
            <div class="stat-number">{{ projectStats.active }}</div>
            <div class="stat-label">进行中</div>
          </div>
        </div>
        <div class="stat-card completed">
          <div class="stat-icon">✅</div>
          <div class="stat-content">
            <div class="stat-number">{{ projectStats.completed }}</div>
            <div class="stat-label">已完成</div>
          </div>
        </div>
        <div class="stat-card delayed">
          <div class="stat-icon">⚠️</div>
          <div class="stat-content">
            <div class="stat-number">{{ projectStats.delayed }}</div>
            <div class="stat-label">延期项目</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters and Search -->
    <div class="filters-section">
      <div class="filters-content">
        <div class="filter-group">
          <label class="filter-label">状态筛选：</label>
          <select v-model="filterStatus" @change="filterProjects" class="filter-select">
            <option value="">全部</option>
            <option value="planning">规划中</option>
            <option value="active">进行中</option>
            <option value="completed">已完成</option>
            <option value="paused">暂停</option>
          </select>
        </div>
        <div class="filter-group">
          <label class="filter-label">优先级：</label>
          <select v-model="filterPriority" @change="filterProjects" class="filter-select">
            <option value="">全部</option>
            <option value="high">高</option>
            <option value="medium">中</option>
            <option value="low">低</option>
          </select>
        </div>
        <div class="filter-group">
          <label class="filter-label">搜索：</label>
          <div class="search-input-wrapper">
            <input 
              type="text"
              v-model="searchText"
              @input="filterProjects"
              placeholder="搜索项目名称..."
              class="search-input"
            />
            <span class="search-icon">🔍</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Project Grid -->
    <div class="projects-section">
      <div class="section-header">
        <h2 class="section-title">项目列表</h2>
        <div class="view-toggles">
          <button 
            class="view-toggle"
            :class="{ active: viewMode === 'grid' }"
            @click="viewMode = 'grid'"
          >
            📊 卡片视图
          </button>
          <button 
            class="view-toggle"
            :class="{ active: viewMode === 'list' }"
            @click="viewMode = 'list'"
          >
            📋 列表视图
          </button>
        </div>
      </div>

      <!-- Grid View -->
      <div v-if="viewMode === 'grid'" class="project-grid">
        <div
          class="project-card"
          v-for="project in filteredProjects"
          :key="project.id"
          :class="getProjectStatusClass(project.status)"
          @click="selectProject(project)"
        >
          <div class="card-header">
            <div class="project-avatar">
              <span class="avatar-text">{{ getProjectInitials(project.name) }}</span>
            </div>
            <div class="project-status">
              <span class="status-badge" :class="getStatusClass(project.status)">
                {{ getStatusText(project.status) }}
              </span>
            </div>
          </div>
          
          <div class="card-content">
            <h3 class="project-title">{{ project.name }}</h3>
            <p class="project-description">{{ project.description || '暂无描述' }}</p>
            
            <div class="project-meta">
              <div class="meta-item">
                <span class="meta-icon">👤</span>
                <span class="meta-text">{{ project.team_size || 0 }}人</span>
              </div>
              <div class="meta-item">
                <span class="meta-icon">📅</span>
                <span class="meta-text">{{ formatDate(project.due_date) }}</span>
              </div>
              <div class="meta-item">
                <span class="meta-icon">⚡</span>
                <span class="priority-tag" :class="getPriorityClass(project.priority)">
                  {{ project.priority || '中' }}
                </span>
              </div>
            </div>

            <div class="task-summary">
              <div class="summary-item">
                <span class="summary-label">总任务：</span>
                <span class="summary-value">{{ project.task_count || 0 }}</span>
              </div>
              <div class="summary-item">
                <span class="summary-label">已完成：</span>
                <span class="summary-value completed">{{ project.completed_count || 0 }}</span>
              </div>
            </div>

            <div class="progress-section">
              <div class="progress-header">
                <span class="progress-label">完成进度</span>
                <span class="progress-percentage">{{ project.progress || 0 }}%</span>
              </div>
              <div class="progress-bar">
                <div
                  class="progress-fill"
                  :style="{ width: (project.progress || 0) + '%' }"
                  :class="getProgressClass(project.progress)"
                ></div>
              </div>
            </div>
          </div>

          <div class="card-actions">
            <button class="action-btn" @click.stop="editProject(project)">
              <span class="action-icon">✏️</span>
            </button>
            <button class="action-btn" @click.stop="deleteProject(project)">
              <span class="action-icon">🗑️</span>
            </button>
            <button class="action-btn primary" @click.stop="viewProjectDetails(project)">
              <span class="action-icon">👁️</span>
            </button>
          </div>
        </div>
      </div>

      <!-- List View -->
      <div v-if="viewMode === 'list'" class="project-table">
        <table class="modern-table">
          <thead>
            <tr>
              <th>项目名称</th>
              <th>状态</th>
              <th>优先级</th>
              <th>负责人</th>
              <th>截止日期</th>
              <th>进度</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="project in filteredProjects" :key="project.id" class="table-row">
              <td>
                <div class="project-info">
                  <div class="project-avatar small">
                    <span class="avatar-text">{{ getProjectInitials(project.name) }}</span>
                  </div>
                  <div class="project-details">
                    <div class="project-name">{{ project.name }}</div>
                    <div class="task-count">{{ project.task_count || 0 }} 个任务</div>
                  </div>
                </div>
              </td>
              <td>
                <span class="status-badge" :class="getStatusClass(project.status)">
                  {{ getStatusText(project.status) }}
                </span>
              </td>
              <td>
                <span class="priority-tag" :class="getPriorityClass(project.priority)">
                  {{ project.priority || '中' }}
                </span>
              </td>
              <td>{{ project.manager || '未分配' }}</td>
              <td>{{ formatDate(project.due_date) }}</td>
              <td>
                <div class="progress-cell">
                  <div class="progress-bar small">
                    <div 
                      class="progress-fill" 
                      :style="{ width: (project.progress || 0) + '%' }"
                      :class="getProgressClass(project.progress)"
                    ></div>
                  </div>
                  <span class="progress-text">{{ project.progress || 0 }}%</span>
                </div>
              </td>
              <td>
                <div class="action-buttons">
                  <button class="btn-action" @click="editProject(project)" title="编辑">
                    ✏️
                  </button>
                  <button class="btn-action" @click="selectProject(project)" title="查看详情">
                    👁️
                  </button>
                  <button class="btn-action danger" @click="deleteProject(project)" title="删除">
                    🗑️
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Project Details Panel -->
    <div v-if="selectedProject" class="details-panel">
      <div class="panel-header">
        <div class="panel-title">
          <h3>《{{ selectedProject.name }}》项目详情</h3>
          <button class="close-btn" @click="selectedProject = null">×</button>
        </div>
      </div>
      
      <div class="panel-content">
        <div class="project-overview">
          <div class="overview-grid">
            <div class="overview-item">
              <div class="overview-label">项目状态</div>
              <span class="status-badge" :class="getStatusClass(selectedProject.status)">
                {{ getStatusText(selectedProject.status) }}
              </span>
            </div>
            <div class="overview-item">
              <div class="overview-label">优先级</div>
              <span class="priority-tag" :class="getPriorityClass(selectedProject.priority)">
                {{ selectedProject.priority || '中' }}
              </span>
            </div>
            <div class="overview-item">
              <div class="overview-label">项目经理</div>
              <div class="overview-value">{{ selectedProject.manager || '未分配' }}</div>
            </div>
            <div class="overview-item">
              <div class="overview-label">团队规模</div>
              <div class="overview-value">{{ selectedProject.team_size || 0 }} 人</div>
            </div>
          </div>
        </div>

        <div class="tasks-section">
          <div class="tasks-header">
            <h4>任务列表</h4>
            <span class="task-stats">
              {{ selectedProject.completed_count || 0 }} / {{ selectedProject.task_count || 0 }} 已完成
            </span>
          </div>
          
          <div class="task-list" v-if="selectedProject.tasks && selectedProject.tasks.length">
            <div 
              class="task-item"
              v-for="task in selectedProject.tasks"
              :key="task.id"
              :class="getTaskStatusClass(task.status)"
            >
              <div class="task-checkbox">
                <input 
                  type="checkbox" 
                  :checked="task.status === 'completed'"
                  @change="toggleTaskStatus(task)"
                />
              </div>
              <div class="task-content">
                <div class="task-name">{{ task.name }}</div>
                <div class="task-meta">
                  <span class="task-assignee">{{ task.assignee || '未分配' }}</span>
                  <span class="task-due">{{ formatDate(task.due_date) }}</span>
                </div>
              </div>
              <div class="task-status">
                <span class="status-dot" :class="getTaskStatusClass(task.status)"></span>
                {{ getTaskStatusText(task.status) }}
              </div>
            </div>
          </div>
          
          <div v-else class="empty-tasks">
            <div class="empty-icon">📝</div>
            <p>该项目暂无任务</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Create Project Modal -->
    <div class="modal-overlay" v-if="showCreateModal" @click="closeCreateModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>新建项目</h3>
          <button class="modal-close" @click="showCreateModal = false">×</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>项目名称</label>
            <input 
              type="text" 
              v-model="newProject.name" 
              placeholder="请输入项目名称"
              class="form-input"
            />
          </div>
          <div class="form-group">
            <label>项目描述</label>
            <textarea 
              v-model="newProject.description" 
              placeholder="请输入项目描述"
              class="form-textarea"
              rows="3"
            ></textarea>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>优先级</label>
              <select v-model="newProject.priority" class="form-select">
                <option value="high">高</option>
                <option value="medium">中</option>
                <option value="low">低</option>
              </select>
            </div>
            <div class="form-group">
              <label>项目经理</label>
              <input 
                type="text" 
                v-model="newProject.manager" 
                placeholder="请输入项目经理"
                class="form-input"
              />
            </div>
          </div>
          <div class="form-group">
            <label>截止日期</label>
            <input 
              type="date" 
              v-model="newProject.due_date"
              class="form-input"
            />
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" @click="showCreateModal = false">取消</button>
          <button class="btn btn-primary" @click="createProject">确认创建</button>
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
  name: 'ProjectPage',
  data() {
    return {
      loading: false,
      viewMode: 'grid', // 'grid' or 'list'
      showCreateModal: false,
      projects: [
        {
          id: 1,
          name: '企业官网重构',
          description: '重新设计和开发企业官方网站',
          status: 'active',
          priority: 'high',
          manager: '张三',
          team_size: 5,
          task_count: 12,
          completed_count: 8,
          progress: 67,
          due_date: '2024-02-15',
          tasks: [
            { id: 1, name: 'UI设计', status: 'completed', assignee: '李设计', due_date: '2024-01-10' },
            { id: 2, name: '前端开发', status: 'active', assignee: '王前端', due_date: '2024-01-20' },
            { id: 3, name: '后端接口', status: 'pending', assignee: '赵后端', due_date: '2024-01-25' }
          ]
        },
        {
          id: 2,
          name: '移动端APP开发',
          description: '开发跨平台移动应用',
          status: 'planning',
          priority: 'medium',
          manager: '李四',
          team_size: 8,
          task_count: 20,
          completed_count: 3,
          progress: 15,
          due_date: '2024-03-30',
          tasks: []
        },
        {
          id: 3,
          name: '数据分析平台',
          description: '构建企业数据分析和可视化平台',
          status: 'completed',
          priority: 'high',
          manager: '王五',
          team_size: 6,
          task_count: 15,
          completed_count: 15,
          progress: 100,
          due_date: '2024-01-05',
          tasks: []
        }
      ],
      filteredProjects: [],
      selectedProject: null,
      filterStatus: '',
      filterPriority: '',
      searchText: '',
      newProject: {
        name: '',
        description: '',
        priority: 'medium',
        manager: '',
        due_date: ''
      }
    }
  },
  computed: {
    projectStats() {
      return {
        total: this.projects.length,
        active: this.projects.filter(p => p.status === 'active').length,
        completed: this.projects.filter(p => p.status === 'completed').length,
        delayed: this.projects.filter(p => this.isProjectDelayed(p)).length
      }
    }
  },
  methods: {
    fetchProjects() {
      this.loading = true;
      // 模拟API调用
      setTimeout(() => {
        this.projects = this.projects.map(p => ({
          ...p,
          progress: p.task_count ? Math.round((p.completed_count / p.task_count) * 100) : 0
        }));
        this.filterProjects();
        this.loading = false;
      }, 500);
    },
    
    filterProjects() {
      let filtered = [...this.projects];
      
      if (this.filterStatus) {
        filtered = filtered.filter(project => project.status === this.filterStatus);
      }
      
      if (this.filterPriority) {
        filtered = filtered.filter(project => project.priority === this.filterPriority);
      }
      
      if (this.searchText) {
        filtered = filtered.filter(project => 
          project.name.toLowerCase().includes(this.searchText.toLowerCase()) ||
          (project.description && project.description.toLowerCase().includes(this.searchText.toLowerCase()))
        );
      }
      
      this.filteredProjects = filtered;
    },
    
    selectProject(project) {
      // 模拟API调用获取任务详情
      this.selectedProject = { ...project };
    },
    
    refreshProjects() {
      this.fetchProjects();
    },
    
    createProject() {
      if (!this.newProject.name.trim()) {
        alert('请输入项目名称');
        return;
      }
      
      const newId = Math.max(...this.projects.map(p => p.id)) + 1;
      const projectToAdd = {
        ...this.newProject,
        id: newId,
        status: 'planning',
        task_count: 0,
        completed_count: 0,
        progress: 0,
        team_size: 1,
        tasks: []
      };
      
      this.projects.push(projectToAdd);
      this.filterProjects();
      this.resetNewProject();
      this.showCreateModal = false;
    },
    
    editProject(project) {
      console.log('编辑项目:', project);
    },
    
    deleteProject(project) {
      if (confirm(`确定要删除项目"${project.name}"吗？`)) {
        this.projects = this.projects.filter(p => p.id !== project.id);
        this.filterProjects();
        if (this.selectedProject && this.selectedProject.id === project.id) {
          this.selectedProject = null;
        }
      }
    },
    
    viewProjectDetails(project) {
      this.selectProject(project);
    },
    
    toggleTaskStatus(task) {
      task.status = task.status === 'completed' ? 'active' : 'completed';
      // 更新项目统计
      if (this.selectedProject) {
        this.selectedProject.completed_count = this.selectedProject.tasks.filter(t => t.status === 'completed').length;
        this.selectedProject.progress = Math.round((this.selectedProject.completed_count / this.selectedProject.task_count) * 100);
      }
    },
    
    resetNewProject() {
      this.newProject = {
        name: '',
        description: '',
        priority: 'medium',
        manager: '',
        due_date: ''
      };
    },
    
    closeCreateModal() {
      this.showCreateModal = false;
    },
    
    // 辅助方法
    getProjectInitials(name) {
      return name.split(' ').map(word => word[0]).join('').toUpperCase().substring(0, 2);
    },
    
    getStatusClass(status) {
      const classes = {
        'planning': 'status-planning',
        'active': 'status-active',
        'completed': 'status-completed',
        'paused': 'status-paused'
      };
      return classes[status] || '';
    },
    
    getStatusText(status) {
      const texts = {
        'planning': '规划中',
        'active': '进行中',
        'completed': '已完成',
        'paused': '暂停'
      };
      return texts[status] || status;
    },
    
    getPriorityClass(priority) {
      const classes = {
        'high': 'priority-high',
        'medium': 'priority-medium',
        'low': 'priority-low'
      };
      return classes[priority] || '';
    },
    
    getProgressClass(progress) {
      if (progress >= 80) return 'progress-high';
      if (progress >= 50) return 'progress-medium';
      return 'progress-low';
    },
    
    getProjectStatusClass(status) {
      return `project-${status}`;
    },
    
    getTaskStatusClass(status) {
      return `task-${status}`;
    },
    
    getTaskStatusText(status) {
      const texts = {
        'pending': '待办',
        'active': '进行中',
        'completed': '已完成'
      };
      return texts[status] || status;
    },
    
    formatDate(dateString) {
      if (!dateString) return '未设置';
      const date = new Date(dateString);
      return date.toLocaleDateString('zh-CN');
    },
    
    isProjectDelayed(project) {
      if (!project.due_date) return false;
      const today = new Date();
      const dueDate = new Date(project.due_date);
      return today > dueDate && project.status !== 'completed';
    }
  },
  
  mounted() {
    this.fetchProjects();
  },
  
  watch: {
    projects: {
      handler() {
        this.filterProjects();
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

.project-page {
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
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

.stat-card.total {
  border-left: 4px solid #6366f1;
}

.stat-card.active {
  border-left: 4px solid #10b981;
}

.stat-card.completed {
  border-left: 4px solid #059669;
}

.stat-card.delayed {
  border-left: 4px solid #ef4444;
}

.stat-card {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.stat-icon {
  font-size: 2rem;
  width: 60px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(99, 102, 241, 0.1);
  border-radius: 12px;
}

.stat-content {
  flex: 1;
}

.stat-number {
  font-size: 2rem;
  font-weight: 700;
  color: #1e293b;
  margin: 0;
}

.stat-label {
  color: #64748b;
  font-size: 0.9rem;
  margin-top: 0.25rem;
}

/* Filters Section */
.filters-section {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border-radius: 12px;
  padding: 1.5rem;
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
  gap: 0.5rem;
}

.filter-label {
  font-weight: 600;
  color: #374151;
  font-size: 0.9rem;
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
  width: 250px;
}

.search-icon {
  position: absolute;
  left: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  color: #9ca3af;
}

/* Projects Section */
.projects-section {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border-radius: 12px;
  padding: 2rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.section-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
  margin: 0;
}

.view-toggles {
  display: flex;
  gap: 0.5rem;
}

.view-toggle {
  padding: 0.5rem 1rem;
  border: 1px solid #e2e8f0;
  background: white;
  border-radius: 6px;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.view-toggle:hover {
  background: #f8fafc;
}

.view-toggle.active {
  background: #6366f1;
  color: white;
  border-color: #6366f1;
}

/* Project Grid */
.project-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 1.5rem;
}

.project-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  border: 1px solid #e2e8f0;
  transition: all 0.3s ease;
  cursor: pointer;
}

.project-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.project-avatar {
  width: 50px;
  height: 50px;
  border-radius: 10px;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 600;
  font-size: 1.1rem;
}

.project-avatar.small {
  width: 40px;
  height: 40px;
  font-size: 0.9rem;
}

.avatar-text {
  color: white;
}

.project-status {
  align-self: flex-start;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.status-planning {
  background: #fef3c7;
  color: #92400e;
}

.status-active {
  background: #d1fae5;
  color: #065f46;
}

.status-completed {
  background: #dcfce7;
  color: #166534;
}

.status-paused {
  background: #fee2e2;
  color: #991b1b;
}

.card-content {
  margin-bottom: 1rem;
}

.project-title {
  font-size: 1.3rem;
  font-weight: 700;
  color: #1e293b;
  margin: 0 0 0.5rem 0;
}

.project-description {
  color: #64748b;
  font-size: 0.9rem;
  line-height: 1.5;
  margin: 0 0 1rem 0;
}

.project-meta {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
  flex-wrap: wrap;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.9rem;
}

.meta-icon {
  font-size: 1rem;
}

.meta-text {
  color: #64748b;
}

.priority-tag {
  padding: 0.2rem 0.5rem;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 600;
}

.priority-high {
  background: #fee2e2;
  color: #991b1b;
}

.priority-medium {
  background: #fef3c7;
  color: #92400e;
}

.priority-low {
  background: #e0e7ff;
  color: #3730a3;
}

.task-summary {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
  padding: 0.75rem;
  background: #f8fafc;
  border-radius: 8px;
}

.summary-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.9rem;
}

.summary-label {
  color: #64748b;
}

.summary-value {
  font-weight: 600;
  color: #1e293b;
}

.summary-value.completed {
  color: #059669;
}

.progress-section {
  margin-bottom: 1rem;
}

.progress-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.5rem;
}

.progress-label {
  font-size: 0.9rem;
  color: #64748b;
}

.progress-percentage {
  font-size: 0.9rem;
  font-weight: 600;
  color: #1e293b;
}

.progress-bar {
  height: 8px;
  background: #e2e8f0;
  border-radius: 4px;
  overflow: hidden;
}

.progress-bar.small {
  height: 6px;
}

.progress-fill {
  height: 100%;
  border-radius: 4px;
  transition: width 0.3s ease;
}

.progress-high {
  background: linear-gradient(90deg, #059669, #10b981);
}

.progress-medium {
  background: linear-gradient(90deg, #d97706, #f59e0b);
}

.progress-low {
  background: linear-gradient(90deg, #dc2626, #ef4444);
}

.card-actions {
  display: flex;
  gap: 0.5rem;
  justify-content: flex-end;
}

.action-btn {
  width: 36px;
  height: 36px;
  border: none;
  border-radius: 8px;
  background: #f1f5f9;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.action-btn:hover {
  background: #e2e8f0;
  transform: scale(1.1);
}

.action-btn.primary {
  background: #6366f1;
  color: white;
}

.action-btn.primary:hover {
  background: #5855eb;
}

.action-icon {
  font-size: 1rem;
}

/* Table View */
.project-table {
  overflow-x: auto;
}

.modern-table {
  width: 100%;
  border-collapse: collapse;
  background: white;
  border-radius: 8px;
  overflow: hidden;
}

.modern-table th,
.modern-table td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid #e2e8f0;
}

.modern-table th {
  background: #f8fafc;
  font-weight: 600;
  color: #374151;
  font-size: 0.9rem;
}

.table-row:hover {
  background: #f8fafc;
}

.project-info {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.project-details {
  flex: 1;
}

.project-name {
  font-weight: 600;
  color: #1e293b;
  margin-bottom: 0.25rem;
}

.task-count {
  font-size: 0.8rem;
  color: #64748b;
}

.progress-cell {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.progress-text {
  font-size: 0.9rem;
  font-weight: 600;
  color: #374151;
  min-width: 40px;
}

.action-buttons {
  display: flex;
  gap: 0.5rem;
}

.btn-action {
  width: 32px;
  height: 32px;
  border: none;
  border-radius: 6px;
  background: #f1f5f9;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.9rem;
}

.btn-action:hover {
  background: #e2e8f0;
  transform: scale(1.1);
}

.btn-action.danger:hover {
  background: #fee2e2;
  color: #dc2626;
}

/* Details Panel */
.details-panel {
  position: fixed;
  top: 0;
  right: 0;
  width: 500px;
  height: 100vh;
  background: white;
  box-shadow: -4px 0 20px rgba(0, 0, 0, 0.15);
  z-index: 1000;
  overflow-y: auto;
}

.panel-header {
  padding: 2rem;
  border-bottom: 1px solid #e2e8f0;
  background: #f8fafc;
}

.panel-title {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.panel-title h3 {
  margin: 0;
  font-size: 1.5rem;
  color: #1e293b;
}

.close-btn {
  width: 40px;
  height: 40px;
  border: none;
  background: #e2e8f0;
  border-radius: 50%;
  cursor: pointer;
  font-size: 1.5rem;
  color: #64748b;
  transition: all 0.2s ease;
}

.close-btn:hover {
  background: #cbd5e1;
  transform: scale(1.1);
}

.panel-content {
  padding: 2rem;
}

.project-overview {
  margin-bottom: 2rem;
}

.overview-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}

.overview-item {
  padding: 1rem;
  background: #f8fafc;
  border-radius: 8px;
}

.overview-label {
  font-size: 0.9rem;
  color: #64748b;
  margin-bottom: 0.5rem;
}

.overview-value {
  font-weight: 600;
  color: #1e293b;
  font-size: 1.1rem;
}

.tasks-section {
  border-top: 1px solid #e2e8f0;
  padding-top: 2rem;
}

.tasks-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.tasks-header h4 {
  margin: 0;
  color: #1e293b;
}

.task-stats {
  font-size: 0.9rem;
  color: #64748b;
}

.task-list {
  gap: 0.75rem;
}

.task-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  background: #f8fafc;
  border-radius: 8px;
  margin-bottom: 0.75rem;
}

.task-checkbox {
  flex-shrink: 0;
}

.task-checkbox input[type="checkbox"] {
  width: 18px;
  height: 18px;
  cursor: pointer;
}

.task-content {
  flex: 1;
}

.task-name {
  font-weight: 600;
  color: #1e293b;
  margin-bottom: 0.25rem;
}

.task-meta {
  display: flex;
  gap: 1rem;
  font-size: 0.8rem;
  color: #64748b;
}

.task-status {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.8rem;
  color: #64748b;
}

.status-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #cbd5e1;
}

.task-pending .status-dot {
  background: #fbbf24;
}

.task-active .status-dot {
  background: #10b981;
}

.task-completed .status-dot {
  background: #059669;
}

.empty-tasks {
  text-align: center;
  padding: 3rem 1rem;
  color: #64748b;
}

.empty-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
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
  border-radius: 12px;
  width: 90%;
  max-width: 600px;
  max-height: 80vh;
  overflow-y: auto;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 2rem;
  border-bottom: 1px solid #e2e8f0;
}

.modal-header h3 {
  margin: 0;
  font-size: 1.5rem;
  color: #1e293b;
}

.modal-close {
  width: 40px;
  height: 40px;
  border: none;
  background: #f1f5f9;
  border-radius: 50%;
  cursor: pointer;
  font-size: 1.5rem;
  color: #64748b;
  transition: all 0.2s ease;
}

.modal-close:hover {
  background: #e2e8f0;
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
.form-select,
.form-textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 1rem;
  transition: border-color 0.2s ease;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
  outline: none;
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.form-textarea {
  resize: vertical;
  min-height: 80px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  padding: 2rem;
  border-top: 1px solid #e2e8f0;
}

/* Loading */
.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.9);
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
  border: 4px solid #e2e8f0;
  border-top: 4px solid #6366f1;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Responsive Design */
@media (max-width: 768px) {
  .project-page {
    padding: 1rem;
  }
  
  .header-content {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }
  
  .header-actions {
    justify-content: center;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .filters-content {
    flex-direction: column;
    align-items: stretch;
  }
  
  .filter-group {
    justify-content: space-between;
  }
  
  .project-grid {
    grid-template-columns: 1fr;
  }
  
  .details-panel {
    width: 100%;
    left: 0;
  }
  
  .overview-grid {
    grid-template-columns: 1fr;
  }
  
  .form-row {
    grid-template-columns: 1fr;
  }
  
  .modal-content {
    width: 95%;
    margin: 2rem;
  }
  
  .section-header {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }
  
  .modern-table {
    font-size: 0.9rem;
  }
  
  .modern-table th,
  .modern-table td {
    padding: 0.75rem 0.5rem;
  }
}
</style>