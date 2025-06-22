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
        <div class="stat-card planning">
          <div class="stat-icon">📋</div>
          <div class="stat-content">
            <div class="stat-number">{{ projectStats.planning }}</div>
            <div class="stat-label">规划中</div>
          </div>
        </div>
        <div class="stat-card active">
          <div class="stat-icon">🚀</div>
          <div class="stat-content">
            <div class="stat-number">{{ projectStats.active }}</div>
            <div class="stat-label">进行中</div>
          </div>
        </div>
        <div class="stat-card on-hold">
          <div class="stat-icon">⏸️</div>
          <div class="stat-content">
            <div class="stat-number">{{ projectStats.on_hold }}</div>
            <div class="stat-label">暂停</div>
          </div>
        </div>
        <div class="stat-card completed">
          <div class="stat-icon">✅</div>
          <div class="stat-content">
            <div class="stat-number">{{ projectStats.completed }}</div>
            <div class="stat-label">已完成</div>
          </div>
        </div>
        <div class="stat-card cancelled">
          <div class="stat-icon">❌</div>
          <div class="stat-content">
            <div class="stat-number">{{ projectStats.cancelled }}</div>
            <div class="stat-label">已取消</div>
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
            <option value="on_hold">暂停</option>
            <option value="completed">已完成</option>
            <option value="cancelled">已取消</option>
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

      <!-- Empty State -->
      <div v-if="filteredProjects.length === 0" class="empty-state">
        <div class="empty-icon">📋</div>
        <h3>暂无项目</h3>
        <p>您目前没有任何项目。点击右上角的"新建项目"按钮开始创建吧！</p>
      </div>

      <!-- Grid View -->
      <div v-else-if="viewMode === 'grid'" class="project-grid">
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
                <span class="meta-icon">👨‍💼</span>
                <span class="meta-text">{{ project.manager || '未分配' }}</span>
              </div>
              <div class="meta-item">
                <span class="meta-icon">👤</span>
                <span class="meta-text">{{ project.team_size || 0 }}人</span>
              </div>
              <div class="meta-item">
                <span class="meta-icon">⚡</span>
                <span class="priority-tag" :class="getPriorityClass(project.priority)">
                  {{ getPriorityText(project.priority) }}
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
                {{ getPriorityText(selectedProject.priority) }}
              </span>
            </div>
            <div class="overview-item">
              <div class="overview-label">项目经理</div>
              <div class="overview-value">{{ selectedProject.manager?.name || '未分配' }}</div>
             </div>
             <div class="overview-item">
               <div class="overview-label">团队规模</div>
               <div class="overview-value">{{ selectedProject.stats?.team_size || 0 }} 人</div>
             </div>
           </div>
         </div>

         <div class="tasks-section">
           <div class="tasks-header">
             <h4>任务列表</h4>
             <span class="task-stats">
               {{ selectedProject.stats?.completed_tasks || 0 }} / {{ selectedProject.stats?.total_tasks || 0 }} 已完成
            </span>
          </div>
          
          <div class="task-list" v-if="selectedProject.tasks && selectedProject.tasks.length">
            <div 
              class="task-item"
              v-for="task in selectedProject.tasks"
              :key="task.id"
              :class="getTaskStatusClass(task.status)"
              @click="showTaskDetails(task)"
            >
              <div class="task-main-info">
                <div class="task-header">
                  <div class="task-title">
                    <span class="task-icon">📋</span>
                    {{ task.title || task.name }}
                  </div>
                  <div class="task-status-badge" :class="getTaskStatusClass(task.status)">
                    <span class="status-dot"></span>
                    {{ {
                      'pending': '待处理',
                      'in_progress': '进行中',
                      'completed': '已完成'
                    }[task.status] || task.status }}
                  </div>
                </div>
                <div class="task-details">
                  <div class="task-priority">
                    <span class="priority-icon">⚡</span>
                    <span class="priority-text" :class="getPriorityClass(task.priority)">
                      {{ getPriorityText(task.priority) }}
                    </span>
                  </div>
                  <div class="task-manager">
                    <span class="manager-icon">👤</span>
                    <span class="manager-text">{{ task.manager_name || '未分配' }}</span>
                  </div>
                  <div class="task-date">
                    <span class="date-icon">📅</span>
                    <span class="date-text">{{ formatDateTime(task.expected_end_time) }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div v-else class="empty-tasks">
            <div class="empty-icon">📝</div>
            <p>该项目暂无任务</p>
          </div>
        </div>

        <!-- 时间信息部分 -->
        <div class="time-section">
          <h4 class="section-title">时间信息</h4>
          <div class="time-grid">
            <div class="time-item">
              <div class="time-label">创建时间</div>
              <div class="time-value">{{ formatDateTime(selectedProject.created_at) }}</div>
            </div>
            <div class="time-item">
              <div class="time-label">预计开始时间</div>
              <div class="time-value">{{ formatDateTime(selectedProject.expected_start_time) }}</div>
            </div>
            <div class="time-item">
              <div class="time-label">实际开始时间</div>
              <div class="time-value">{{ formatDateTime(selectedProject.actual_start_time) }}</div>
            </div>
            <div class="time-item">
              <div class="time-label">预计结束时间</div>
              <div class="time-value">{{ formatDateTime(selectedProject.expected_end_time) }}</div>
            </div>
            <div class="time-item">
              <div class="time-label">实际结束时间</div>
              <div class="time-value">{{ formatDateTime(selectedProject.actual_end_time) }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Task Details Modal -->
    <div class="modal-overlay" v-if="showTaskModal" @click="closeTaskModal">
      <div class="modal-content task-modal" @click.stop>
        <div class="modal-header" style="background-color: #f8f9fa; border-bottom: 1px solid #e9ecef; padding: 1rem 1.5rem;">
          <h3 style="margin: 0; color: #495057; font-size: 1.25rem;">任务详情</h3>
          <button class="modal-close" @click="showTaskModal = false" style="background: none; border: none; font-size: 1.5rem; color: #6c757d; cursor: pointer; padding: 0.5rem;">×</button>
        </div>
        <div class="modal-body" v-if="selectedTask">
          <!-- 基本信息 -->
          <div class="task-basic-info">
            <div class="info-row">
              <div class="info-label">任务标题</div>
              <div class="info-value">{{ selectedTask.title || selectedTask.name }}</div>
            </div>
            <div class="info-row">
              <div class="info-label">任务描述</div>
              <div class="info-value">{{ selectedTask.description || '暂无描述' }}</div>
            </div>
            <div class="info-row">
              <div class="info-label">任务状态</div>
              <div class="info-value">
                <span class="status-badge" :class="getTaskStatusClass(selectedTask.status)" style="padding: 0.5rem 1rem; border-radius: 20px; font-weight: 500;">
                  {{ {
                    'pending': '待处理',
                    'in_progress': '进行中',
                    'completed': '已完成'
                  }[selectedTask.status] || selectedTask.status }}
                </span>
              </div>
            </div>
            <div class="info-row">
              <div class="info-label">任务优先级</div>
              <div class="info-value">
                <span class="priority-tag" :class="getPriorityClass(selectedTask.priority)">
                  {{ getPriorityText(selectedTask.priority) }}
                </span>
              </div>
            </div>
            <div class="info-row">
              <div class="info-label">任务负责人</div>
              <div class="info-value">{{ selectedTask.manager_name || '未分配' }}</div>
            </div>
          </div>

          <!-- 时间信息 -->
          <div class="task-time-info" style="background-color: #f8f9fa; border-radius: 8px; padding: 1rem; margin-top: 1rem;">
            <h4 style="color: #495057; margin-top: 0;">时间信息</h4>
            <div class="time-grid">
              <div class="time-item">
                <div class="time-label">创建时间</div>
                <div class="time-value">{{ formatDateTime(selectedTask.created_at) }}</div>
              </div>
              <div class="time-item">
                <div class="time-label">预计开始时间</div>
                <div class="time-value">{{ formatDateTime(selectedTask.expected_start_time) }}</div>
              </div>
              <div class="time-item">
                <div class="time-label">实际开始时间</div>
                <div class="time-value">{{ formatDateTime(selectedTask.actual_start_time) }}</div>
              </div>
              <div class="time-item">
                <div class="time-label">预计结束时间</div>
                <div class="time-value">{{ formatDateTime(selectedTask.expected_end_time) }}</div>
              </div>
              <div class="time-item">
                <div class="time-label">实际结束时间</div>
                <div class="time-value">{{ formatDateTime(selectedTask.actual_end_time) }}</div>
              </div>
            </div>
          </div>

          <!-- 项目关系信息 -->
          <div class="task-project-info" style="background-color: #f8f9fa; border-radius: 8px; padding: 1rem; margin-top: 1rem;">
            <h4 style="color: #495057; margin-top: 0;">项目关系</h4>
            <div class="project-relation">
              <div class="relation-item">
                <div class="relation-label">是否关键任务</div>
                <div class="relation-value">
                  <span class="critical-badge" :class="{ critical: selectedTask.is_critical }">
                    {{ selectedTask.is_critical ? '是' : '否' }}
                  </span>
                </div>
              </div>
              <div class="relation-item">
                <div class="relation-label">完成权重</div>
                <div class="relation-value">{{ selectedTask.completion_weight || 0 }}%</div>
              </div>
            </div>
          </div>

          <!-- 任务成员 -->
          <div class="task-members-info" style="background-color: #f8f9fa; border-radius: 8px; padding: 1rem; margin-top: 1rem;">
            <h4 style="color: #495057; margin-top: 0;">任务成员</h4>
            <div class="members-list" v-if="selectedTask.assigned_users && selectedTask.assigned_users.length">
              <div class="member-item" v-for="member in selectedTask.assigned_users" :key="member.id">
                <div class="member-avatar">
                  <span class="avatar-text">{{ getInitials(member.name) }}</span>
                </div>
                <div class="member-details">
                  <div class="member-name">{{ member.name }}</div>
                  <div class="member-email">{{ member.email }}</div>
                  <div class="member-role">{{ member.pivot?.role || '成员' }}</div>
                  <div class="member-work" v-if="member.pivot?.work_description">
                    工作描述：{{ member.pivot.work_description }}
                  </div>
                  <div class="member-assigned-time" v-if="member.pivot?.assigned_at">
                    分配时间：{{ formatDateTime(member.pivot.assigned_at) }}
                  </div>
                </div>
              </div>
            </div>
            <div v-else class="no-members">
              <p>暂无分配成员</p>
            </div>
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
              <div class="manager-selector">
                <select v-model="newProject.manager_id" class="form-select" @change="onManagerChange">
                  <option value="">请选择项目经理</option>
                  <option :value="currentUser.id">{{ currentUser.name }} (我)</option>
                  <option v-for="contact in contacts" :key="contact.id" :value="contact.id">
                    {{ contact.name }}
                  </option>
                  <option value="add_new">通过邮箱添加新联系人</option>
                </select>
                <div v-if="showAddContactInput" class="add-contact-input">
                  <input 
                    type="email" 
                    v-model="newContactEmail" 
                    placeholder="请输入邮箱地址"
                    class="form-input"
                    @blur="addNewContact"
                    @keyup.enter="addNewContact"
                  />
                  <button type="button" @click="cancelAddContact" class="btn-cancel">取消</button>
                </div>
              </div>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>预计开始时间</label>
              <input 
                type="datetime-local" 
                v-model="newProject.expected_start_time"
                class="form-input"
              />
            </div>
            <div class="form-group">
              <label>预计结束时间</label>
              <input 
                type="datetime-local" 
                v-model="newProject.expected_end_time"
                class="form-input"
              />
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" @click="showCreateModal = false">取消</button>
          <button class="btn btn-primary" @click="createProject">确认创建</button>
        </div>
      </div>
    </div>

    <!-- Edit Project Modal -->
    <div class="modal-overlay" v-if="showEditModal" @click="closeEditModal">
      <div class="modal-content large" @click.stop>
        <div class="modal-header">
          <h3>编辑项目</h3>
          <button class="modal-close" @click="showEditModal = false">×</button>
        </div>
        <div class="modal-body">
          <div class="edit-tabs">
            <button 
              class="tab-btn" 
              :class="{ active: activeTab === 'basic' }"
              @click="activeTab = 'basic'"
            >
              基本信息
            </button>
            <button 
              class="tab-btn" 
              :class="{ active: activeTab === 'members' }"
              @click="activeTab = 'members'"
            >
              项目成员
            </button>
          </div>
          
          <!-- 基本信息标签页 -->
          <div v-if="activeTab === 'basic'" class="tab-content">
            <div class="form-group">
              <label>项目名称</label>
              <input 
                type="text" 
                v-model="editingProject.name" 
                placeholder="请输入项目名称"
                class="form-input"
              />
            </div>
            <div class="form-group">
              <label>项目描述</label>
              <textarea 
                v-model="editingProject.description" 
                placeholder="请输入项目描述"
                class="form-textarea"
                rows="3"
              ></textarea>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>项目状态</label>
                <select v-model="editingProject.status" class="form-select">
                  <option value="planning">规划中</option>
                  <option value="active">进行中</option>
                  <option value="on_hold">暂停</option>
                  <option value="completed">已完成</option>
                  <option value="cancelled">已取消</option>
                </select>
              </div>
              <div class="form-group">
                <label>优先级</label>
                <select v-model="editingProject.priority" class="form-select">
                  <option value="urgent">紧急</option>
                  <option value="high">高</option>
                  <option value="medium">中</option>
                  <option value="low">低</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label>项目经理</label>
              <div class="manager-selector">
                <select v-model="editingProject.manager_id" class="form-select" @change="onEditManagerChange">
                  <option value="">请选择项目经理</option>
                  <option :value="currentUser.id">{{ currentUser.name }} (我)</option>
                  <option v-for="contact in contacts" :key="contact.id" :value="contact.id">
                    {{ contact.name }}
                  </option>
                  <option value="add_new">通过邮箱添加新联系人</option>
                </select>
                <div v-if="showEditAddContactInput" class="add-contact-input">
                  <input 
                    type="email" 
                    v-model="editContactEmail" 
                    placeholder="请输入邮箱地址"
                    class="form-input"
                    @blur="addNewEditContact"
                    @keyup.enter="addNewEditContact"
                  />
                  <button type="button" @click="cancelEditAddContact" class="btn-cancel">取消</button>
                </div>
              </div>
            </div>
          </div>
          
          <!-- 项目成员标签页 -->
          <div v-if="activeTab === 'members'" class="tab-content">
            <div class="members-section">
              <div class="section-header">
                <h4>当前项目成员</h4>
                <button class="btn btn-primary btn-sm" @click="showAddMemberInput = true">添加成员</button>
              </div>
              
              <!-- 添加成员输入框 -->
              <div v-if="showAddMemberInput" class="add-member-form">
                <div class="form-row">
                  <div class="form-group">
                    <select v-model="newMemberId" class="form-select">
                      <option value="">从通讯录选择</option>
                      <option v-for="contact in availableContacts" :key="contact.id" :value="contact.id">
                        {{ contact.name }}
                      </option>
                    </select>
                  </div>
                  <div class="form-group">
                    <input 
                      type="email" 
                      v-model="newMemberEmail" 
                      placeholder="或输入邮箱地址"
                      class="form-input"
                    />
                  </div>
                  <div class="form-actions">
                    <button class="btn btn-primary btn-sm" @click="addProjectMember">添加</button>
                    <button class="btn btn-secondary btn-sm" @click="cancelAddMember">取消</button>
                  </div>
                </div>
              </div>
              
              <!-- 成员列表 -->
              <div class="members-list">
                <div v-for="member in editingProject.members" :key="member.id" class="member-item">
                  <div class="member-info">
                    <div class="member-avatar">
                      <span>{{ getProjectInitials(member.name) }}</span>
                    </div>
                    <div class="member-details">
                      <div class="member-name">{{ member.name }}</div>
                      <div class="member-email">{{ member.email }}</div>
                      <div class="member-role">{{ member.role || '成员' }}</div>
                    </div>
                  </div>
                  <div class="member-actions">
                    <button 
                      v-if="member.id !== editingProject.manager_id" 
                      class="btn btn-danger btn-sm" 
                      @click="removeMember(member.id)"
                    >
                      移除
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" @click="showEditModal = false">取消</button>
          <button class="btn btn-primary" @click="updateProject">保存修改</button>
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
          created_at: '2024-01-01T09:00:00',
          expected_start_time: '2024-01-05T09:00:00',
          actual_start_time: '2024-01-08T10:30:00',
          expected_end_time: '2024-02-15T18:00:00',
          actual_end_time: null,
          tasks: [
            { id: 1, title: 'UI设计', name: 'UI设计', status: 'completed', priority: 'high', assignee: '李设计', due_date: '2024-01-10' },
            { id: 2, title: '前端开发', name: '前端开发', status: 'active', priority: 'medium', assignee: '王前端', due_date: '2024-01-20' },
            { id: 3, title: '后端接口', name: '后端接口', status: 'pending', priority: 'urgent', assignee: '赵后端', due_date: '2024-01-25' }
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
          created_at: '2024-01-10T14:00:00',
          expected_start_time: '2024-02-01T09:00:00',
          actual_start_time: null,
          expected_end_time: '2024-03-30T18:00:00',
          actual_end_time: null,
          tasks: []
        },
        {
          id: 3,
          name: '数据分析平台',
          description: '构建企业数据分析和可视化平台',
          status: 'completed',
          priority: 'urgent',
          manager: '王五',
          team_size: 6,
          task_count: 15,
          completed_count: 15,
          progress: 100,
          created_at: '2023-12-01T09:00:00',
          expected_start_time: '2023-12-05T09:00:00',
          actual_start_time: '2023-12-05T09:15:00',
          expected_end_time: '2024-01-05T18:00:00',
          actual_end_time: '2024-01-03T16:30:00',
          tasks: []
        },
        {
          id: 4,
          name: '客户管理系统',
          description: '开发CRM客户关系管理系统',
          status: 'on_hold',
          priority: 'low',
          manager: '赵六',
          team_size: 4,
          task_count: 8,
          completed_count: 2,
          progress: 25,
          created_at: '2024-01-15T11:00:00',
          expected_start_time: '2024-01-20T09:00:00',
          actual_start_time: '2024-01-22T10:00:00',
          expected_end_time: '2024-04-15T18:00:00',
          actual_end_time: null,
          tasks: []
        },
        {
          id: 5,
          name: '旧版系统迁移',
          description: '将旧版系统数据迁移到新平台',
          status: 'cancelled',
          priority: 'medium',
          manager: '孙七',
          team_size: 3,
          task_count: 6,
          completed_count: 1,
          progress: 17,
          created_at: '2023-11-15T13:00:00',
          expected_start_time: '2023-12-01T09:00:00',
          actual_start_time: '2023-12-03T09:30:00',
          expected_end_time: '2024-01-31T18:00:00',
          actual_end_time: null,
          tasks: []
        }
      ],
      filteredProjects: [],
      selectedProject: null,
      filterStatus: '',
      filterPriority: '',
      searchText: '',
      showCreateModal: false,
      showEditModal: false,
      editingProject: {},
      // 演示用户数据
      currentUser: {
        id: 1,
        name: '张三',
        email: 'zhangsan@example.com'
      },
      // 演示通讯录数据
      contacts: [
        { id: 2, name: '李四', email: 'lisi@example.com' },
        { id: 3, name: '王五', email: 'wangwu@example.com' },
        { id: 4, name: '赵六', email: 'zhaoliu@example.com' },
        { id: 5, name: '钱七', email: 'qianqi@example.com' },
        { id: 6, name: '孙八', email: 'sunba@example.com' },
        { id: 7, name: '周九', email: 'zhoujiu@example.com' },
        { id: 8, name: '吴十', email: 'wushi@example.com' }
      ],
      newProject: {
        name: '',
        description: '',
        priority: 'medium',
        manager_id: '',
        manager_name: '',
        expected_start_time: '',
        expected_end_time: ''
      },
      newContactEmail: '',
      showAddContactInput: false,
      // 编辑弹窗相关
      activeTab: 'basic',
      showEditAddContactInput: false,
      editContactEmail: '',
      showAddMemberInput: false,
      newMemberId: '',
      newMemberEmail: '',
      // 任务详情弹窗相关
      showTaskModal: false,
      selectedTask: null
    }
  },
  computed: {
    projectStats() {
      return {
        total: this.projects.length,
        planning: this.projects.filter(p => p.status === 'planning').length,
        active: this.projects.filter(p => p.status === 'active').length,
        on_hold: this.projects.filter(p => p.status === 'on_hold').length,
        completed: this.projects.filter(p => p.status === 'completed').length,
        cancelled: this.projects.filter(p => p.status === 'cancelled').length
      }
    },
    availableContacts() {
      if (!this.editingProject.members) return this.contacts;
      const memberIds = this.editingProject.members.map(m => m.id);
      return this.contacts.filter(contact => !memberIds.includes(contact.id));
    }
  },
  methods: {
    async fetchProjects() {
      this.loading = true;
      try {
        const token = localStorage.getItem('token');
        
        if (token) {
          // 已登录用户获取真实数据
          const response = await this.$axios.get('/api/projects');
          
          if (response.data.success) {
            console.log('后端返回的项目数据:', response.data.data);
            // 使用后端返回的数据，并确保每个项目都包含stats属性和成员信息
            this.projects = (response.data.data || []).map(project => {
              console.log('单个项目数据:', project);
              console.log('项目经理信息:', project.manager);
              return ({
              ...project,
              team_size: project.stats?.team_size || 0,
              task_count: project.stats?.total_tasks || 0,
              completed_count: project.stats?.completed_tasks || 0,
              progress: project.stats?.progress || 0,
              manager: project.manager?.name || '未分配',
              manager_id: project.manager?.id || null,
              members: project.users || [],
              team_members: (project.users || []).map(user => user.name)
            });
          });
          this.filterProjects();
          } else {
            // API调用失败，显示错误消息
            this.$message.error(response.data.message || '获取项目数据失败');
            console.error('获取项目数据失败:', response.data.message);
            
            // 在开发环境下使用演示数据
            if (process.env.NODE_ENV === 'development') {
              this.projects = this.getDefaultProjects();
              this.filterProjects();
              this.$message.warning('当前显示的是演示数据');
            } else {
              this.projects = [];
              this.filterProjects();
            }
          }
        } else {
          // 未登录用户使用演示数据
          this.projects = this.getDefaultProjects();
          this.filterProjects();
          this.$message.info('请登录后查看真实项目数据');
        }
      } catch (error) {
        // 显示具体错误信息
        const errorMessage = error.response?.data?.message || error.message || '获取项目数据失败';
        this.$message.error(errorMessage);
        console.error('获取项目数据出错:', error);
        
        // 在开发环境下使用演示数据
        if (process.env.NODE_ENV === 'development') {
          this.projects = this.getDefaultProjects();
          this.filterProjects();
          this.$message.warning('当前显示的是演示数据');
        } else {
          this.projects = [];
          this.filterProjects();
        }
      } finally {
        this.loading = false;
      }
    },
    
    async fetchContacts() {
      try {
        const token = localStorage.getItem('token');
        
        if (token) {
          // 已登录用户获取真实通讯录数据
          const response = await this.$axios.get('/api/contacts');
          
          if (response.data.success) {
            this.contacts = response.data.data;
          } else {
            // API调用失败，显示错误消息
            this.$message.error(response.data.message || '获取通讯录数据失败');
            console.error('获取通讯录数据失败:', response.data.message);
            
            // 在开发环境下保持使用演示数据
            if (process.env.NODE_ENV === 'development') {
              this.$message.warning('当前显示的是演示通讯录数据');
            } else {
              this.contacts = [];
            }
          }
        } else {
          // 未登录用户使用默认演示数据
          this.$message.info('请登录后查看真实通讯录数据');
        }
      } catch (error) {
        // 显示具体错误信息
        const errorMessage = error.response?.data?.message || error.message || '获取通讯录数据失败';
        this.$message.error(errorMessage);
        console.error('获取通讯录数据出错:', error);
        
        // 在开发环境下保持使用演示数据
        if (process.env.NODE_ENV === 'development') {
          this.$message.warning('当前显示的是演示通讯录数据');
        } else {
          this.contacts = [];
        }
      }
    },
    
    getDefaultProjects() {
      return [
        {
          id: 1,
          name: '企业官网重构',
          description: '重新设计和开发企业官方网站，提升用户体验和品牌形象。包括响应式设计、性能优化、SEO优化、内容管理系统升级等。',
          status: 'active',
          priority: 'high',
          expected_start_time: '2024-01-01T00:00:00.000000Z',
          expected_end_time: '2024-02-15T00:00:00.000000Z',
          actual_start_time: '2024-01-01T00:00:00.000000Z',
          actual_end_time: null,
          created_at: '2024-01-01T00:00:00.000000Z',
          updated_at: '2024-01-15T00:00:00.000000Z',
          stats: {
            total_tasks: 24,
            completed_tasks: 16,
            in_progress_tasks: 6,
            pending_tasks: 2,
            team_size: 5,
            progress: 67,
            is_delayed: false
          },
          manager: '张三',
          team_members: ['张三', '李四', '王五', '赵六', '钱七'],
          tasks: [
            { title: '需求分析与规划', status: 'completed', priority: 'high' },
            { title: '原型设计', status: 'completed', priority: 'high' },
            { title: 'UI设计', status: 'completed', priority: 'high' },
            { title: '首页开发', status: 'completed', priority: 'high' },
            { title: '产品页面开发', status: 'completed', priority: 'medium' },
            { title: '新闻模块开发', status: 'completed', priority: 'medium' },
            { title: '后台管理系统开发', status: 'in_progress', priority: 'high' },
            { title: '性能优化', status: 'pending', priority: 'medium' }
          ]
        },
        {
          id: 2,
          name: '移动端APP开发',
          description: '开发跨平台移动应用，支持iOS和Android系统。采用Flutter框架，实现用户认证、消息推送、数据同步、离线存储等核心功能。',
          status: 'planning',
          priority: 'medium',
          expected_start_time: '2024-02-01T00:00:00.000000Z',
          expected_end_time: '2024-03-30T00:00:00.000000Z',
          actual_start_time: null,
          actual_end_time: null,
          created_at: '2024-01-20T00:00:00.000000Z',
          updated_at: '2024-01-25T00:00:00.000000Z',
          stats: {
            total_tasks: 30,
            completed_tasks: 5,
            in_progress_tasks: 3,
            pending_tasks: 22,
            team_size: 8,
            progress: 15,
            is_delayed: false
          },
          manager: '李四',
          team_members: ['李四', '王五', '赵六', '钱七', '孙八', '周九', '吴十', '郑一'],
          tasks: [
            { title: '项目初始化与配置', status: 'completed', priority: 'high' },
            { title: '用户认证模块', status: 'in_progress', priority: 'high' },
            { title: '首页UI开发', status: 'in_progress', priority: 'medium' },
            { title: '数据同步功能', status: 'pending', priority: 'high' },
            { title: '消息推送系统', status: 'pending', priority: 'medium' },
            { title: '离线存储实现', status: 'pending', priority: 'medium' }
          ]
        },
        {
          id: 3,
          name: '数据分析平台',
          description: '构建企业数据分析和可视化平台，支持多维度数据展示。整合各业务系统数据，提供实时监控、趋势分析、预测分析等功能。',
          status: 'completed',
          priority: 'high',
          expected_start_time: '2023-11-01T00:00:00.000000Z',
          expected_end_time: '2024-01-05T00:00:00.000000Z',
          actual_start_time: '2023-11-01T00:00:00.000000Z',
          actual_end_time: '2024-01-05T00:00:00.000000Z',
          created_at: '2023-11-01T00:00:00.000000Z',
          updated_at: '2024-01-05T00:00:00.000000Z',
          stats: {
            total_tasks: 20,
            completed_tasks: 20,
            in_progress_tasks: 0,
            pending_tasks: 0,
            team_size: 6,
            progress: 100,
            is_delayed: false
          },
          manager: '王五',
          team_members: ['王五', '赵六', '钱七', '孙八', '周九', '吴十'],
          tasks: [
            { title: '数据接口开发', status: 'completed', priority: 'high' },
            { title: '数据清洗与转换', status: 'completed', priority: 'high' },
            { title: '可视化组件开发', status: 'completed', priority: 'high' },
            { title: '实时监控模块', status: 'completed', priority: 'high' },
            { title: '报表生成系统', status: 'completed', priority: 'medium' },
            { title: '用户权限管理', status: 'completed', priority: 'medium' }
          ]
        },
        {
          id: 4,
          name: '智能客服系统',
          description: '开发基于AI的智能客服系统，集成自然语言处理、知识图谱、情感分析等技术，提供7x24小时智能客服服务。',
          status: 'on_hold',
          priority: 'urgent',
          expected_start_time: '2024-01-10T00:00:00.000000Z',
          expected_end_time: '2024-03-20T00:00:00.000000Z',
          actual_start_time: '2024-01-10T00:00:00.000000Z',
          actual_end_time: null,
          created_at: '2024-01-05T00:00:00.000000Z',
          updated_at: '2024-01-28T00:00:00.000000Z',
          stats: {
            total_tasks: 18,
            completed_tasks: 6,
            in_progress_tasks: 4,
            pending_tasks: 8,
            team_size: 7,
            progress: 33,
            is_delayed: true
          },
          manager: '赵六',
          team_members: ['赵六', '钱七', '孙八', '周九', '吴十', '郑一', '冯二'],
          tasks: [
            { title: 'NLP引擎集成', status: 'completed', priority: 'urgent' },
            { title: '知识库构建', status: 'in_progress', priority: 'high' },
            { title: '对话流程设计', status: 'in_progress', priority: 'high' },
            { title: '情感分析模块', status: 'pending', priority: 'medium' },
            { title: '多轮对话支持', status: 'pending', priority: 'high' }
          ]
        },
        {
          id: 5,
          name: '供应链管理系统',
          description: '构建现代化供应链管理系统，实现采购、库存、物流全流程数字化管理，提供实时追踪、智能预警、数据分析等功能。',
          status: 'cancelled',
          priority: 'medium',
          expected_start_time: '2023-12-01T00:00:00.000000Z',
          expected_end_time: '2024-02-28T00:00:00.000000Z',
          actual_start_time: '2023-12-01T00:00:00.000000Z',
          actual_end_time: '2024-01-15T00:00:00.000000Z',
          created_at: '2023-11-25T00:00:00.000000Z',
          updated_at: '2024-01-15T00:00:00.000000Z',
          stats: {
            total_tasks: 25,
            completed_tasks: 8,
            in_progress_tasks: 0,
            pending_tasks: 17,
            team_size: 10,
            progress: 32,
            is_delayed: true
          },
          manager: '钱七',
          team_members: ['钱七', '孙八', '周九', '吴十', '郑一', '冯二', '陈三', '楚四', '魏五', '蒋六'],
          tasks: [
            { title: '需求调研', status: 'completed', priority: 'high' },
            { title: '系统架构设计', status: 'completed', priority: 'high' },
            { title: '数据库设计', status: 'completed', priority: 'high' },
            { title: '采购模块开发', status: 'pending', priority: 'medium' },
            { title: '库存管理模块', status: 'pending', priority: 'medium' },
            { title: '物流跟踪系统', status: 'pending', priority: 'medium' }
          ]
        }
      ];
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
    
    async selectProject(project) {
      try {
        const token = localStorage.getItem('token');
        let response;
        
        if (token) {
          // 已登录用户获取真实项目详情
          response = await this.$axios.get(`/api/projects/${project.id}`);
          if (response.data.success) {
            const projectData = response.data.data;
            // 补充 manager 信息
            projectData.manager = {
              id: projectData.manager_id,
              name: this.projects.find(p => p.id === project.id)?.manager || '未分配'
            };
            this.selectedProject = projectData;
            console.log('selectProject方法中的selectedProject:', this.selectedProject);
          } else {
            this.selectedProject = { ...project };
          }
        } else {
          // 未登录用户使用当前项目数据
          this.selectedProject = { ...project };
        }
      } catch (error) {
        console.error('获取项目详情出错:', error);
        this.selectedProject = { ...project };
      }
    },
    
    refreshProjects() {
      this.fetchProjects();
    },
    
    async createProject() {
      if (!this.newProject.name || !this.newProject.description) {
        alert('请填写项目名称和描述');
        return;
      }
      
      const token = localStorage.getItem('token');
      if (!token) {
        alert('请先登录后再创建项目');
        return;
      }
      
      try {
         const response = await this.$axios.post('/api/projects', this.newProject);
        
        if (response.data.success) {
          // 重新获取项目列表
          await this.fetchProjects();
          
          // 重置表单
          this.newProject = {
            name: '',
            description: '',
            priority: 'medium',
            manager: '',
            expected_start_time: '',
            expected_end_time: ''
          };
          
          this.showCreateModal = false;
          alert('项目创建成功！');
        } else {
          alert('创建项目失败: ' + response.data.message);
        }
      } catch (error) {
        console.error('创建项目出错:', error);
        alert('创建项目失败，请稍后重试');
      }
    },
    
    editProject(project) {
      // 深拷贝项目数据，避免直接修改原数据
      this.editingProject = {
        ...project,
        members: project.members || [
          { id: project.manager_id || 1, name: project.manager || '张三', email: 'zhangsan@example.com', role: '项目经理' },
          { id: 2, name: '李四', email: 'lisi@example.com', role: '开发工程师' },
          { id: 3, name: '王五', email: 'wangwu@example.com', role: '测试工程师' }
        ]
      };
      this.activeTab = 'basic';
      this.showEditModal = true;
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
        manager_id: '',
        manager_name: '',
        expected_start_time: '',
        expected_end_time: ''
      };
    },
    
    // 新建项目经理选择相关方法
    onManagerChange() {
      if (this.newProject.manager_id === 'add_new') {
        this.showAddContactInput = true;
        this.newProject.manager_id = '';
      } else {
        this.showAddContactInput = false;
        // 设置经理名称
        if (this.newProject.manager_id === this.currentUser.id) {
          this.newProject.manager_name = this.currentUser.name;
        } else {
          const contact = this.contacts.find(c => c.id === this.newProject.manager_id);
          this.newProject.manager_name = contact ? contact.name : '';
        }
      }
    },
    
    async addNewContact() {
      if (this.newContactEmail && this.isValidEmail(this.newContactEmail)) {
        try {
          const token = localStorage.getItem('token');
          
          if (token) {
            // 已登录用户调用API添加联系人
            const response = await this.$axios.post('/api/contacts/add-by-email', {
              email: this.newContactEmail,
              contact_name: this.newContactEmail.split('@')[0]
            });
            
            if (response.data.success) {
              const newContact = response.data.data;
              this.contacts.push(newContact);
              this.newProject.manager_id = newContact.id;
              this.newProject.manager_name = newContact.name;
              this.$message.success('联系人添加成功');
            } else {
              this.$message.error(response.data.message || '添加联系人失败');
            }
          } else {
            // 未登录用户使用本地数据
            const newContact = {
              id: Date.now(),
              name: this.newContactEmail.split('@')[0],
              email: this.newContactEmail
            };
            this.contacts.push(newContact);
            this.newProject.manager_id = newContact.id;
            this.newProject.manager_name = newContact.name;
          }
          
          this.newContactEmail = '';
          this.showAddContactInput = false;
        } catch (error) {
          console.error('添加联系人出错:', error);
          this.$message.error('添加联系人失败');
        }
      }
    },
    
    cancelAddContact() {
      this.newContactEmail = '';
      this.showAddContactInput = false;
    },
    
    // 编辑项目相关方法
    onEditManagerChange() {
      if (this.editingProject.manager_id === 'add_new') {
        this.showEditAddContactInput = true;
        this.editingProject.manager_id = '';
      } else {
        this.showEditAddContactInput = false;
        // 设置经理名称
        if (this.editingProject.manager_id === this.currentUser.id) {
          this.editingProject.manager_name = this.currentUser.name;
        } else {
          const contact = this.contacts.find(c => c.id === this.editingProject.manager_id);
          this.editingProject.manager_name = contact ? contact.name : '';
        }
      }
    },
    
    async addNewEditContact() {
      if (this.editContactEmail && this.isValidEmail(this.editContactEmail)) {
        try {
          const token = localStorage.getItem('token');
          
          if (token) {
            // 已登录用户调用API添加联系人
            const response = await this.$axios.post('/api/contacts/add-by-email', {
              email: this.editContactEmail,
              contact_name: this.editContactEmail.split('@')[0]
            });
            
            if (response.data.success) {
              const newContact = response.data.data;
              this.contacts.push(newContact);
              this.editingProject.manager_id = newContact.id;
              this.editingProject.manager_name = newContact.name;
              this.$message.success('联系人添加成功');
            } else {
              this.$message.error(response.data.message || '添加联系人失败');
            }
          } else {
            // 未登录用户使用本地数据
            const newContact = {
              id: Date.now(),
              name: this.editContactEmail.split('@')[0],
              email: this.editContactEmail
            };
            this.contacts.push(newContact);
            this.editingProject.manager_id = newContact.id;
            this.editingProject.manager_name = newContact.name;
          }
          
          this.editContactEmail = '';
          this.showEditAddContactInput = false;
        } catch (error) {
          console.error('添加联系人出错:', error);
          this.$message.error('添加联系人失败');
        }
      }
    },
    
    cancelEditAddContact() {
      this.editContactEmail = '';
      this.showEditAddContactInput = false;
    },
    
    addProjectMember() {
      let newMember = null;
      
      if (this.newMemberId) {
        // 从通讯录添加
        const contact = this.contacts.find(c => c.id === this.newMemberId);
        if (contact) {
          newMember = {
            id: contact.id,
            name: contact.name,
            email: contact.email,
            role: '成员'
          };
        }
      } else if (this.newMemberEmail && this.isValidEmail(this.newMemberEmail)) {
        // 通过邮箱添加
        newMember = {
          id: Date.now(),
          name: this.newMemberEmail.split('@')[0],
          email: this.newMemberEmail,
          role: '成员'
        };
        // 同时添加到通讯录
        this.contacts.push({
          id: newMember.id,
          name: newMember.name,
          email: newMember.email
        });
      }
      
      if (newMember) {
        if (!this.editingProject.members) {
          this.editingProject.members = [];
        }
        this.editingProject.members.push(newMember);
        this.cancelAddMember();
      }
    },
    
    cancelAddMember() {
      this.newMemberId = '';
      this.newMemberEmail = '';
      this.showAddMemberInput = false;
    },
    
    removeMember(memberId) {
      if (confirm('确定要移除该成员吗？')) {
        this.editingProject.members = this.editingProject.members.filter(m => m.id !== memberId);
      }
    },
    
    updateProject() {
      // 更新项目数据
      const projectIndex = this.projects.findIndex(p => p.id === this.editingProject.id);
      if (projectIndex !== -1) {
        this.projects[projectIndex] = { ...this.editingProject };
        this.filterProjects();
        
        // 如果当前选中的是被编辑的项目，更新选中项目
        if (this.selectedProject && this.selectedProject.id === this.editingProject.id) {
          this.selectedProject = { ...this.editingProject };
        }
      }
      
      this.showEditModal = false;
      alert('项目更新成功！');
    },
    
    closeEditModal() {
      this.showEditModal = false;
      this.activeTab = 'basic';
      this.showEditAddContactInput = false;
      this.showAddMemberInput = false;
      this.editContactEmail = '';
      this.newMemberId = '';
      this.newMemberEmail = '';
    },
    
    isValidEmail(email) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return emailRegex.test(email);
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
        'on_hold': '暂停',
        'completed': '已完成',
        'cancelled': '已取消'
      };
      return texts[status] || status;
    },
    
    getPriorityClass(priority) {
      const classes = {
        'urgent': 'priority-urgent',
        'high': 'priority-high',
        'medium': 'priority-medium',
        'low': 'priority-low'
      };
      return classes[priority] || '';
    },

    getPriorityText(priority) {
      const texts = {
        'urgent': '紧急',
        'high': '高',
        'medium': '中',
        'low': '低'
      };
      return texts[priority] || '中';
    },
    
    getProgressClass(progress) {
      if (progress >= 90) return 'progress-excellent';
      if (progress >= 75) return 'progress-high';
      if (progress >= 50) return 'progress-medium';
      if (progress >= 25) return 'progress-low';
      return 'progress-minimal';
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

    formatDateTime(dateString) {
      if (!dateString) return '未设置';
      const date = new Date(dateString);
      return date.toLocaleString('zh-CN', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
      });
    },
    
    isProjectDelayed(project) {
      if (!project.due_date) return false;
      const today = new Date();
      const dueDate = new Date(project.due_date);
      return today > dueDate && project.status !== 'completed';
    },

    // 任务详情相关方法
    async showTaskDetails(task) {
      // console.log('开始获取任务详情:', task);
      try {
        const token = localStorage.getItem('token');
        // console.log('用户token状态:', token ? '已登录' : '未登录');
        if (token) {
          // 已登录用户获取真实任务详情
          const response = await this.$axios.get(`/api/tasks/${task.id}`);
          // console.log('获取到的任务详情响应:', response.data);
          if (response.data.success) {
            const taskData = response.data.data;
            console.log('解析的任务数据:', taskData);
            this.selectedTask = {
              ...taskData,
              title: taskData.title || taskData.name,
              team_members: taskData.assigned_users || []
            };
          } else {
            this.selectedTask = task;
          }
        } else {
          // 未登录用户使用当前任务数据
          this.selectedTask = task;
        }
      } catch (error) {
        console.error('获取任务详情出错:', error);
        this.selectedTask = task;
      }
      console.log('最终设置的任务详情:', this.selectedTask);
      
      // 如果没有分配用户数据，使用默认值
      if (!this.selectedTask.team_members || this.selectedTask.team_members.length === 0) {
        this.selectedTask.team_members = [
          {
            id: 1,
            name: '张三',
            email: 'zhangsan@example.com',
            role: '项目经理',
            work_description: '负责项目整体规划和进度管理'
          },
          {
            id: 2,
            name: '李四',
            email: 'lisi@example.com',
            role: '开发工程师',
            work_description: '负责前端界面开发和用户交互实现'
          },
          {
            id: 3,
            name: '王五',
            email: 'wangwu@example.com',
            role: '测试工程师',
            work_description: '负责功能测试和质量保证'
          }
        ]
      };
      this.showTaskModal = true;
    },

    closeTaskModal() {
      this.showTaskModal = false;
      this.selectedTask = null;
    },

    getInitials(name) {
      if (!name) return '';
      return name.length > 1 ? name.substring(0, 2) : name;
    }
  },
  
  mounted() {
    this.fetchProjects();
    this.fetchContacts();
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
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
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

.stat-card.planning {
  border-left: 4px solid #8b5cf6;
}

.stat-card.active {
  border-left: 4px solid #10b981;
}

.stat-card.on-hold {
  border-left: 4px solid #f59e0b;
}

.stat-card.completed {
  border-left: 4px solid #059669;
}

.stat-card.cancelled {
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

.priority-urgent {
  background: #fecaca;
  color: #dc2626;
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

.progress-excellent {
  background: linear-gradient(90deg, #059669, #10b981);
  box-shadow: 0 2px 4px rgba(5, 150, 105, 0.3);
}

.progress-high {
  background: linear-gradient(90deg, #0891b2, #06b6d4);
  box-shadow: 0 2px 4px rgba(8, 145, 178, 0.3);
}

.progress-medium {
  background: linear-gradient(90deg, #d97706, #f59e0b);
  box-shadow: 0 2px 4px rgba(217, 119, 6, 0.3);
}

.progress-low {
  background: linear-gradient(90deg, #dc2626, #ef4444);
  box-shadow: 0 2px 4px rgba(220, 38, 38, 0.3);
}

.progress-minimal {
  background: linear-gradient(90deg, #64748b, #94a3b8);
  box-shadow: 0 2px 4px rgba(100, 116, 139, 0.3);
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

/* Time Section */
.time-section {
  margin-bottom: 2rem;
  padding: 1.5rem;
  background: #f8fafc;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
}

.section-title {
  margin: 0 0 1.5rem 0;
  font-size: 1.2rem;
  font-weight: 600;
  color: #1e293b;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.section-title::before {
  content: '🕒';
  font-size: 1.1rem;
}

.time-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.time-item {
  background: white;
  padding: 1rem;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  transition: all 0.2s ease;
}

.time-item:hover {
  border-color: #cbd5e1;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.time-label {
  font-size: 0.85rem;
  color: #64748b;
  margin-bottom: 0.5rem;
  font-weight: 500;
}

.time-value {
  font-weight: 600;
  color: #1e293b;
  font-size: 0.95rem;
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
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.task-item {
  background: white;
  border-radius: 12px;
  padding: 1rem;
  margin-bottom: 0.75rem;
  border: 1px solid #e2e8f0;
  transition: all 0.2s ease;
  cursor: pointer;
}

.task-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  border-color: #cbd5e1;
}

.task-main-info {
  width: 100%;
}

.task-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.75rem;
}

.task-title {
  font-weight: 600;
  color: #1e293b;
  font-size: 1rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.task-icon {
  font-size: 1.1rem;
}

.task-status-badge {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.4rem 0.8rem;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 500;
}

.task-status-badge.pending {
  background-color: #fff8e6;
  color: #b88700;
}

.task-status-badge.in_progress {
  background-color: #e6f3ff;
  color: #0066cc;
}

.task-status-badge.completed {
  background-color: #e6ffe6;
  color: #00994d;
}

.status-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
}

.pending .status-dot {
  background-color: #ffc107;
}

.in_progress .status-dot {
  background-color: #2196f3;
}

.completed .status-dot {
  background-color: #4caf50;
}

.task-details {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  color: #64748b;
  font-size: 0.875rem;
}

.task-priority,
.task-manager,
.task-date {
  display: flex;
  align-items: center;
  gap: 0.4rem;
}

.priority-icon,
.manager-icon,
.date-icon {
  font-size: 1rem;
}

.priority-text {
  font-weight: 500;
}

.priority-text.urgent {
  color: #dc2626;
}

.priority-text.high {
  color: #ea580c;
}

.priority-text.medium {
  color: #0284c7;
}

.priority-text.low {
  color: #64748b;
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

/* 编辑弹窗样式 */
.modal-content.large {
  max-width: 800px;
  max-height: 90vh;
  overflow-y: auto;
}

.edit-tabs {
  display: flex;
  border-bottom: 2px solid #e5e7eb;
  margin-bottom: 1.5rem;
}

.tab-btn {
  padding: 0.75rem 1.5rem;
  border: none;
  background: none;
  color: #6b7280;
  font-weight: 500;
  cursor: pointer;
  border-bottom: 2px solid transparent;
  transition: all 0.2s;
}

.tab-btn.active {
  color: #3b82f6;
  border-bottom-color: #3b82f6;
}

.tab-btn:hover {
  color: #3b82f6;
}

.tab-content {
  animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

/* 项目经理选择器样式 */
.manager-selector {
  position: relative;
}

.add-contact-input {
  display: flex;
  gap: 0.5rem;
  margin-top: 0.5rem;
  align-items: center;
}

.btn-cancel {
  padding: 0.5rem 1rem;
  background: #ef4444;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.875rem;
}

.btn-cancel:hover {
  background: #dc2626;
}

/* 成员管理样式 */
.members-section {
  background: #f8fafc;
  border-radius: 8px;
  padding: 1.5rem;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.section-header h4 {
  margin: 0;
  color: #1e293b;
}

.btn-sm {
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
}

.add-member-form {
  background: white;
  border-radius: 8px;
  padding: 1rem;
  margin-bottom: 1rem;
  border: 1px solid #e5e7eb;
}

.form-actions {
  display: flex;
  gap: 0.5rem;
  align-items: center;
}

.members-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.member-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  background: white;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
}

.member-info {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.member-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 600;
  font-size: 0.875rem;
}

.member-details {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.member-name {
  font-weight: 600;
  color: #1e293b;
}

.member-email {
  font-size: 0.875rem;
  color: #6b7280;
}

.member-role {
  font-size: 0.75rem;
  color: #3b82f6;
  background: #eff6ff;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  display: inline-block;
}

.member-actions {
  display: flex;
  gap: 0.5rem;
}

.btn-danger {
  background: #ef4444;
  color: white;
}

.btn-danger:hover {
  background: #dc2626;
}

/* 任务详情弹窗样式 */
.task-modal {
  max-width: 800px;
  max-height: 90vh;
  overflow-y: auto;
}

.task-basic-info {
  margin-bottom: 2rem;
}

.info-row {
  display: flex;
  margin-bottom: 1rem;
  align-items: flex-start;
}

.info-label {
  width: 120px;
  font-weight: 600;
  color: #333;
  flex-shrink: 0;
}

.info-value {
  flex: 1;
  color: #666;
}

.status-badge {
  padding: 6px 16px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 500;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.status-badge::before {
  content: '';
  width: 8px;
  height: 8px;
  border-radius: 50%;
  display: inline-block;
}

.status-badge.pending {
  background-color: #fff8e6;
  color: #b88700;
}

.status-badge.pending::before {
  background-color: #ffc107;
}

.status-badge.in_progress {
  background-color: #e6f3ff;
  color: #0066cc;
}

.status-badge.in_progress::before {
  background-color: #2196f3;
}

.status-badge.completed {
  background-color: #e6ffe6;
  color: #00994d;
}

.status-badge.completed::before {
  background-color: #4caf50;
}

.priority-tag {
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
}

.priority-urgent {
  background-color: #fee2e2;
  color: #dc2626;
}

.priority-high {
  background-color: #fef3c7;
  color: #d97706;
}

.priority-medium {
  background-color: #dbeafe;
  color: #2563eb;
}

.priority-low {
  background-color: #f3f4f6;
  color: #6b7280;
}

.task-time-info {
  margin-bottom: 2rem;
}

.task-time-info h4 {
  margin-bottom: 1rem;
  color: #333;
  font-size: 16px;
}

.time-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.time-item {
  background: #f8fafc;
  padding: 12px;
  border-radius: 6px;
}

.time-label {
  font-size: 12px;
  color: #64748b;
  margin-bottom: 4px;
}

.time-value {
  font-size: 14px;
  color: #334155;
  font-weight: 500;
}

.task-project-info {
  margin-bottom: 2rem;
}

.task-project-info h4 {
  margin-bottom: 1rem;
  color: #333;
  font-size: 16px;
}

.project-relation {
  display: flex;
  gap: 2rem;
}

.relation-item {
  display: flex;
  align-items: center;
  gap: 8px;
}

.relation-label {
  font-size: 14px;
  color: #64748b;
}

.relation-value {
  font-size: 14px;
  color: #334155;
  font-weight: 500;
}

.critical-badge {
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
  background-color: #f3f4f6;
  color: #6b7280;
}

.critical-badge.critical {
  background-color: #fee2e2;
  color: #dc2626;
}

.task-members-info h4 {
  margin-bottom: 1rem;
  color: #333;
  font-size: 16px;
}

.task-members-info .members-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.task-members-info .member-item {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 12px;
  background: #f8fafc;
  border-radius: 8px;
  border: none;
}

.task-members-info .member-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.task-members-info .avatar-text {
  color: white;
  font-size: 14px;
  font-weight: 600;
}

.task-members-info .member-details {
  flex: 1;
}

.task-members-info .member-name {
  font-size: 14px;
  font-weight: 600;
  color: #334155;
  margin-bottom: 2px;
}

.task-members-info .member-email {
  font-size: 12px;
  color: #64748b;
  margin-bottom: 2px;
}

.task-members-info .member-role {
  font-size: 12px;
  color: #3b82f6;
  margin-bottom: 4px;
  background: none;
  padding: 0;
}

.member-work {
  font-size: 12px;
  color: #64748b;
  line-height: 1.4;
}

.no-members {
  text-align: center;
  padding: 2rem;
  color: #64748b;
}

/* 响应式设计 - 任务弹窗 */
@media (max-width: 768px) {
  .task-modal {
    max-height: 95vh;
  }
  
  .time-grid {
    grid-template-columns: 1fr;
  }
  
  .project-relation {
    flex-direction: column;
    gap: 1rem;
  }
  
  .info-row {
    flex-direction: column;
    gap: 4px;
  }
  
  .info-label {
    width: auto;
  }
}

/* 空状态样式 */
.empty-state {
  text-align: center;
  padding: 3rem 1rem;
  background: #f8f9fa;
  border-radius: 8px;
  margin: 1rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.empty-state .empty-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
  color: #6c757d;
}

.empty-state h3 {
  font-size: 1.5rem;
  color: #343a40;
  margin-bottom: 0.5rem;
  font-weight: 600;
}

.empty-state p {
  color: #6c757d;
  font-size: 1rem;
  max-width: 400px;
  margin: 0 auto;
  line-height: 1.5;
}
</style>