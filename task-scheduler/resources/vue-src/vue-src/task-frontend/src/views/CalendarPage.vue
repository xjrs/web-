<template>
  <div class="calendar-page">
    <!-- Animated background elements -->
    <div class="bg-decoration">
      <div class="floating-shape shape-1"></div>
      <div class="floating-shape shape-2"></div>
      <div class="floating-shape shape-3"></div>
      <div class="floating-shape shape-4"></div>
    </div>

    <div class="calendar-container">
      <!-- Header Section -->
      <div class="header-section">
        <div class="header-content">
          <div class="main-icon">
            <div class="icon-wrapper">
              <span class="icon-text">📅</span>
            </div>
          </div>
          <div class="header-text">
            <h1 class="animated-title">日程安排</h1>
            <p class="subtitle">高效规划，掌控时间节奏</p>
          </div>
        </div>
        
        <!-- View Controls -->
        <div class="controls-section">
          <div class="view-selector">
            <button 
              v-for="view in viewOptions" 
              :key="view.value"
              :class="['view-btn', { active: currentView === view.value }]"
              @click="changeView(view.value)"
            >
              <span class="view-icon">{{ view.icon }}</span>
              <span class="view-label">{{ view.label }}</span>
            </button>
          </div>
          <button class="add-event-btn" @click="showAddDialog = true">
            <span class="btn-icon">✨</span>
            <span>添加日程</span>
          </button>
        </div>
      </div>

      <!-- Calendar Main Card -->
      <div class="calendar-card">
        <div class="calendar-wrapper">
          <FullCalendar
            :options="calendarOptions"
            ref="fullCalendar"
            class="custom-calendar"
          />
        </div>

        <!-- Loading Animation -->
        <div v-if="loading" class="loading-overlay">
          <div class="loading-spinner">
            <div class="spinner-ring"></div>
            <p class="loading-text">✨ 加载中...</p>
          </div>
        </div>
      </div>

      <!-- Quick Stats -->
      <div class="stats-section">
        <div class="stat-card">
          <div class="stat-icon">📋</div>
          <div class="stat-info">
            <span class="stat-number">{{ totalEvents }}</span>
            <span class="stat-label">总任务</span>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">⏰</div>
          <div class="stat-info">
            <span class="stat-number">{{ todayEvents }}</span>
            <span class="stat-label">今日任务</span>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">🎯</div>
          <div class="stat-info">
            <span class="stat-number">{{ completedEvents }}</span>
            <span class="stat-label">已完成</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Event Dialog -->
    <div v-if="showAddDialog" class="dialog-overlay" @click="closeDialog">
      <div class="dialog-card" @click.stop>
        <div class="dialog-header">
          <div class="dialog-icon">
            <span>✨</span>
          </div>
          <div class="dialog-title">
            <h3>创建新日程</h3>
            <p>添加重要事项到您的日程表</p>
          </div>
          <button class="close-btn" @click="closeDialog">
            <span>✕</span>
          </button>
        </div>
        
        <form @submit.prevent="addEvent" class="event-form">
          <div class="form-group">
            <label class="form-label">
              <span class="label-icon">📝</span>
              <span>任务标题</span>
            </label>
            <input 
              v-model="newEvent.title" 
              type="text" 
              placeholder="请输入任务标题..." 
              class="form-input"
              required
            >
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label">
                <span class="label-icon">🕐</span>
                <span>开始时间</span>
              </label>
              <input 
                v-model="newEvent.start" 
                type="datetime-local" 
                class="form-input"
                required
              >
            </div>
            <div class="form-group">
              <label class="form-label">
                <span class="label-icon">🕒</span>
                <span>结束时间</span>
              </label>
              <input 
                v-model="newEvent.end" 
                type="datetime-local" 
                class="form-input"
              >
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">
              <span class="label-icon">🏷️</span>
              <span>任务类型</span>
            </label>
            <div class="type-selector">
              <label 
                v-for="type in eventTypes" 
                :key="type.value"
                :class="['type-option', { active: newEvent.type === type.value }]"
              >
                <input 
                  type="radio" 
                  :value="type.value" 
                  v-model="newEvent.type"
                  style="display: none;"
                >
                <span class="type-icon">{{ type.icon }}</span>
                <span class="type-label">{{ type.label }}</span>
              </label>
            </div>
          </div>

          <div class="form-actions">
            <button type="button" class="cancel-btn" @click="closeDialog">
              <span>取消</span>
            </button>
            <button type="submit" class="submit-btn">
              <span class="btn-icon">🚀</span>
              <span>创建任务</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Event Detail Dialog -->
    <div v-if="showEventDialog" class="dialog-overlay" @click="closeEventDialog">
      <div class="dialog-card event-detail-card" @click.stop>
        <div class="dialog-header">
          <div class="dialog-icon">
            <span>{{ getEventTypeIcon(selectedEvent?.extendedProps?.type) }}</span>
          </div>
          <div class="dialog-title">
            <h3>{{ selectedEvent?.title }}</h3>
            <p>任务详细信息</p>
          </div>
          <button class="close-btn" @click="closeEventDialog">
            <span>✕</span>
          </button>
        </div>
        
        <div class="event-details">
          <div class="detail-item">
            <div class="detail-icon">🕐</div>
            <div class="detail-content">
              <span class="detail-label">开始时间</span>
              <span class="detail-value">{{ formatDateTime(selectedEvent?.start) }}</span>
            </div>
          </div>
          
          <div class="detail-item" v-if="selectedEvent?.end">
            <div class="detail-icon">🕒</div>
            <div class="detail-content">
              <span class="detail-label">结束时间</span>
              <span class="detail-value">{{ formatDateTime(selectedEvent?.end) }}</span>
            </div>
          </div>
          
          <div class="detail-item">
            <div class="detail-icon">🏷️</div>
            <div class="detail-content">
              <span class="detail-label">任务类型</span>
              <span class="detail-value">{{ getEventTypeLabel(selectedEvent?.extendedProps?.type) }}</span>
            </div>
          </div>
        </div>

        <div class="event-actions">
          <button class="action-btn edit-btn" @click="editEvent">
            <span class="btn-icon">✏️</span>
            <span>编辑</span>
          </button>
          <button class="action-btn delete-btn" @click="deleteEvent">
            <span class="btn-icon">🗑️</span>
            <span>删除</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'

export default {
  name: 'CalendarPage',
  components: { FullCalendar },
  data() {
    return {
      loading: false,
      currentView: 'dayGridMonth',
      showAddDialog: false,
      showEventDialog: false,
      selectedEvent: null,
      totalEvents: 0,
      todayEvents: 0,
      completedEvents: 0,
      viewOptions: [
        { label: '月视图', value: 'dayGridMonth', icon: '📊' },
        { label: '周视图', value: 'timeGridWeek', icon: '📈' },
        { label: '日视图', value: 'timeGridDay', icon: '📋' }
      ],
      eventTypes: [
        { label: '工作', value: 'work', icon: '💼' },
        { label: '个人', value: 'personal', icon: '🏠' },
        { label: '会议', value: 'meeting', icon: '👥' },
        { label: '其他', value: 'other', icon: '📌' }
      ],
      newEvent: {
        title: '',
        start: '',
        end: '',
        type: 'work'
      },
      calendarOptions: {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        initialView: 'dayGridMonth',
        locale: 'zh-cn',
        firstDay: 1,
        height: 'auto',
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: ''
        },
        buttonText: {
          today: '今天',
          month: '月',
          week: '周',
          day: '日'
        },
        events: [],
        eventClick: this.handleEventClick,
        dateClick: this.handleDateClick,
        eventDidMount: this.styleEvent,
        dayMaxEvents: 3,
        moreLinkText: '更多',
        eventTimeFormat: {
          hour: '2-digit',
          minute: '2-digit',
          hour12: false
        }
      }
    }
  },
  mounted() {
    this.loadTasks()
    // Add entrance animation
    this.$nextTick(() => {
      const container = document.querySelector('.calendar-container');
      if (container) {
        container.style.animation = 'slideInUp 0.8s ease-out';
      }
    });
  },
  methods: {
    async loadTasks() {
      this.loading = true
      try {
        const response = await fetch("/api/tasks")
        const data = await response.json()
        
        const events = data.map(task => ({
          id: task.id,
          title: task.name,
          start: task.start_time,
          end: task.due_date || task.start_time,
          extendedProps: {
            type: task.type || 'work',
            description: task.description,
            completed: task.completed || false
          }
        }))
        
        this.calendarOptions.events = events
        this.updateStats(events)
      } catch (error) {
        console.error('加载任务失败:', error)
        // Mock data for demo
        const mockEvents = [
          { id: 1, title: '项目会议', start: new Date().toISOString(), extendedProps: { type: 'meeting' } },
          { id: 2, title: '代码评审', start: new Date(Date.now() + 86400000).toISOString(), extendedProps: { type: 'work' } }
        ]
        this.calendarOptions.events = mockEvents
        this.updateStats(mockEvents)
      } finally {
        this.loading = false
      }
    },

    updateStats(events) {
      this.totalEvents = events.length
      const today = new Date().toDateString()
      this.todayEvents = events.filter(event => 
        new Date(event.start).toDateString() === today
      ).length
      this.completedEvents = events.filter(event => 
        event.extendedProps?.completed
      ).length
    },
    
    changeView(view) {
      this.currentView = view
      const calendarApi = this.$refs.fullCalendar.getApi()
      calendarApi.changeView(view)
    },
    
    handleEventClick(info) {
      this.selectedEvent = info.event
      this.showEventDialog = true
    },
    
    handleDateClick(info) {
      const selectedDate = new Date(info.dateStr)
      const now = new Date()
      
      // Set default time
      selectedDate.setHours(now.getHours() + 1, 0, 0, 0)
      const endTime = new Date(selectedDate)
      endTime.setHours(selectedDate.getHours() + 1)
      
      this.newEvent.start = selectedDate.toISOString().slice(0, 16)
      this.newEvent.end = endTime.toISOString().slice(0, 16)
      this.showAddDialog = true
    },
    
    styleEvent(info) {
      const eventType = info.event.extendedProps.type || 'work'
      const typeColors = {
        work: { bg: '#667eea', border: '#5a67d8' },
        personal: { bg: '#48bb78', border: '#38a169' },
        meeting: { bg: '#ed8936', border: '#dd6b20' },
        other: { bg: '#9f7aea', border: '#805ad5' }
      }
      
      const colors = typeColors[eventType]
      info.el.style.backgroundColor = colors.bg
      info.el.style.borderColor = colors.border
      info.el.style.borderRadius = '8px'
      info.el.style.border = `2px solid ${colors.border}`
    },

    getEventTypeIcon(type) {
      const icons = {
        work: '💼',
        personal: '🏠', 
        meeting: '👥',
        other: '📌'
      }
      return icons[type] || '📌'
    },

    getEventTypeLabel(type) {
      const labels = {
        work: '工作',
        personal: '个人',
        meeting: '会议', 
        other: '其他'
      }
      return labels[type] || '未知'
    },
    
    async addEvent() {
      try {
        const eventData = {
          name: this.newEvent.title,
          start_time: this.newEvent.start,
          due_date: this.newEvent.end,
          type: this.newEvent.type
        }
        
        const response = await fetch('/api/tasks', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(eventData)
        })
        
        if (response.ok) {
          await this.loadTasks()
          this.closeDialog()
        }
      } catch (error) {
        console.error('添加日程失败:', error)
      }
    },
    
    async deleteEvent() {
      if (!this.selectedEvent) return
      
      try {
        const response = await fetch(`/api/tasks/${this.selectedEvent.id}`, {
          method: 'DELETE'
        })
        
        if (response.ok) {
          await this.loadTasks()
          this.closeEventDialog()
        }
      } catch (error) {
        console.error('删除日程失败:', error)
      }
    },
    
    editEvent() {
      console.log('编辑事件:', this.selectedEvent)
      // TODO: 实现编辑功能
    },
    
    closeDialog() {
      this.showAddDialog = false
      this.resetNewEvent()
    },
    
    closeEventDialog() {
      this.showEventDialog = false
      this.selectedEvent = null
    },
    
    resetNewEvent() {
      this.newEvent = {
        title: '',
        start: '',
        end: '',
        type: 'work'
      }
    },
    
    formatDateTime(date) {
      if (!date) return ''
      return new Date(date).toLocaleString('zh-CN', {
        year: 'numeric',
        month: '2-digit', 
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
      })
    }
  }
}
</script>

<style scoped>
.calendar-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
  position: relative;
  overflow-x: hidden;
  padding: 20px;
}

/* Animated background */
.bg-decoration {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
  z-index: 0;
}

.floating-shape {
  position: absolute;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 50%;
  animation: float 8s ease-in-out infinite;
}

.shape-1 {
  width: 100px;
  height: 100px;
  top: 10%;
  left: 5%;
  animation-delay: 0s;
}

.shape-2 {
  width: 150px;
  height: 150px;
  top: 50%;
  right: 10%;
  animation-delay: 2s;
}

.shape-3 {
  width: 80px;
  height: 80px;
  bottom: 30%;
  left: 15%;
  animation-delay: 4s;
}

.shape-4 {
  width: 120px;
  height: 120px;
  top: 70%;
  right: 25%;
  animation-delay: 6s;
}

@keyframes float {
  0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.7; }
  25% { transform: translateY(-30px) rotate(90deg); opacity: 1; }
  50% { transform: translateY(-15px) rotate(180deg); opacity: 0.8; }
  75% { transform: translateY(-25px) rotate(270deg); opacity: 0.9; }
}

/* Main container */
.calendar-container {
  position: relative;
  z-index: 1;
  max-width: 1400px;
  margin: 0 auto;
}

/* Header section */
.header-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  flex-wrap: wrap;
  gap: 20px;
}

.header-content {
  display: flex;
  align-items: center;
  gap: 20px;
}

.main-icon {
  display: flex;
  align-items: center;
}

.icon-wrapper {
  width: 70px;
  height: 70px;
  background: linear-gradient(135deg, #ff6b6b, #ffa726);
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  animation: pulse 2s infinite;
  box-shadow: 0 10px 30px rgba(255, 107, 107, 0.3);
}

.icon-text {
  font-size: 28px;
  animation: bounce 2s infinite;
}

@keyframes pulse {
  0% { transform: scale(1); }
  50% { transform: scale(1.05); }
  100% { transform: scale(1); }
}

@keyframes bounce {
  0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
  40% { transform: translateY(-3px); }
  60% { transform: translateY(-2px); }
}

.header-text {
  text-align: left;
}

.animated-title {
  margin: 0 0 8px 0;
  font-size: 32px;
  color: white;
  font-weight: 700;
  text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
}

.subtitle {
  margin: 0;
  color: rgba(255, 255, 255, 0.9);
  font-size: 16px;
  opacity: 0.9;
}

/* Controls section */
.controls-section {
  display: flex;
  gap: 20px;
  align-items: center;
  flex-wrap: wrap;
}

.view-selector {
  display: flex;
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(10px);
  border-radius: 15px;
  padding: 6px;
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.view-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  border: none;
  background: transparent;
  border-radius: 10px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  color: rgba(255, 255, 255, 0.8);
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.view-btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
  transition: left 0.5s;
}

.view-btn:hover::before {
  left: 100%;
}

.view-btn:hover {
  color: white;
  background: rgba(255, 255, 255, 0.1);
}

.view-btn.active {
  background: rgba(255, 255, 255, 0.2);
  color: white;
  box-shadow: 0 4px 15px rgba(255, 255, 255, 0.2);
}

.view-icon {
  font-size: 16px;
}

.add-event-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  background: linear-gradient(135deg, #ff6b6b, #ffa726);
  color: white;
  border: none;
  border-radius: 15px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  box-shadow: 0 8px 25px rgba(255, 107, 107, 0.4);
  position: relative;
  overflow: hidden;
}

.add-event-btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.5s;
}

.add-event-btn:hover::before {
  left: 100%;
}

.add-event-btn:hover {
  transform: translateY(-2px) scale(1.02);
  box-shadow: 0 12px 35px rgba(255, 107, 107, 0.5);
}

/* Calendar card */
.calendar-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: 25px;
  padding: 30px;
  box-shadow: 
    0 20px 40px rgba(0, 0, 0, 0.1),
    0 10px 20px rgba(0, 0, 0, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.2);
  position: relative;
  margin-bottom: 30px;
}

.calendar-wrapper {
  position: relative;
}

/* Loading overlay */
.loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(10px);
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 25px;
  z-index: 10;
}

.loading-spinner {
  text-align: center;
}

.spinner-ring {
  width: 50px;
  height: 50px;
  border: 4px solid rgba(102, 126, 234, 0.2);
  border-top: 4px solid #667eea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 15px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.loading-text {
  color: #667eea;
  font-size: 16px;
  font-weight: 500;
  margin: 0;
}

/* Stats section */
.stats-section {
  display: flex;
  gap: 20px;
  justify-content: center;
  flex-wrap: wrap;
}

.stat-card {
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(10px);
  border-radius: 20px;
  padding: 25px;
  display: flex;
  align-items: center;
  gap: 15px;
  border: 1px solid rgba(255, 255, 255, 0.2);
  transition: all 0.3s ease;
  cursor: pointer;
  min-width: 150px;
}

.stat-card:hover {
  transform: translateY(-5px);
  background: rgba(255, 255, 255, 0.2);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}

.stat-icon {
  font-size: 24px;
  width: 50px;
  height: 50px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 15px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.stat-number {
  font-size: 24px;
  font-weight: 700;
  color: white;
}

.stat-label {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.8);
  font-weight: 500;
}

/* Dialog styles */
.dialog-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(10px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 20px;
}

.dialog-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: 25px;
  width: 100%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 
    0 25px 50px rgba(0, 0, 0, 0.2),
    0 15px 30px rgba(0, 0, 0, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  animation: dialogSlideIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

@keyframes dialogSlideIn {
  from {
    opacity: 0;
    transform: translateY(-30px) scale(0.9);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.dialog-header {
  display: flex;
  align-items: center;
  gap: 15px;
  padding: 30px 30px 20px;
  border-bottom: 1px solid rgba(0, 0, 0, 0.1);
}

.dialog-icon {
  width: 50px;
  height: 50px;
  background: linear-gradient(135deg, #667eea, #764ba2);
  border-radius: 15px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.dialog-title {
  flex: 1;
}

.dialog-title h3 {
  margin: 0 0 5px 0;
  font-size: 20px;
  font-weight: 600;
  color: #333;
}

.dialog-title p {
  margin: 0;
  font-size: 14px;
  color: #666;
}

.close-btn {
  width: 40px;
  height: 40px;
  background: rgba(255, 107, 107, 0.1);
  border: none;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  color: #ff6b6b;
  font-size: 18px;
  font-weight: bold;
}

.close-btn:hover {
  background: rgba(255, 107, 107, 0.2);
  transform: scale(1.1);
}

/* Form styles */
.event-form {
  padding: 20px 30px 30px;
}

.form-group {
  margin-bottom: 20px;
}

.form-row {
  display: flex;
  gap: 15px;
}

.form-row .form-group {
  flex: 1;
}

.form-label {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 8px;
  font-size: 14px;
  font-weight: 600;
  color: #333;
}

.label-icon {
  font-size: 16px;
}

.form-input {
  width: 100%;
  padding: 15px 18px;
  border: 2px solid rgba(0, 0, 0, 0.1);
  border-radius: 15px;
  font-size: 14px;
  background: rgba(255, 255, 255, 0.8);
  transition: all 0.3s ease;
  outline: none;
}

.form-input:focus {
  border-color: #667eea;
  background: white;
  box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.form-input::placeholder {
  color: #999;
}

/* Type selector */
.type-selector {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
  gap: 10px;
}

.type-option {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  padding: 15px;
  background: rgba(0, 0, 0, 0.05);
  border: 2px solid transparent;
  border-radius: 15px;
  cursor: pointer;
  transition: all 0.3s ease;
  text-align: center;
}

.type-option:hover {
  background: rgba(102, 126, 234, 0.1);
  border-color: rgba(102, 126, 234, 0.2);
}

.type-option.active {
  background: rgba(102, 126, 234, 0.15);
  border-color: #667eea;
  box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
}

.type-icon {
  font-size: 20px;
}

.type-label {
  font-size: 12px;
  font-weight: 500;
  color: #333;
}

/* Form actions */
.form-actions {
  display: flex;
  gap: 15px;
  justify-content: flex-end;
  margin-top: 25px;
  padding-top: 20px;
  border-top: 1px solid rgba(0, 0, 0, 0.1);
}

.cancel-btn {
  padding: 12px 24px;
  background: rgba(0, 0, 0, 0.05);
  border: 2px solid rgba(0, 0, 0, 0.1);
  border-radius: 12px;
  font-size: 14px;
  font-weight: 500;
  color: #666;
  cursor: pointer;
  transition: all 0.3s ease;
}

.cancel-btn:hover {
  background: rgba(0, 0, 0, 0.1);
  border-color: rgba(0, 0, 0, 0.2);
}

.submit-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
  border: none;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.submit-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 30px rgba(102, 126, 234, 0.4);
}

/* Event detail dialog */
.event-detail-card {
  max-width: 500px;
}

.event-details {
  padding: 20px 30px;
}

.detail-item {
  display: flex;
  align-items: center;
  gap: 15px;
  padding: 15px 0;
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.detail-item:last-child {
  border-bottom: none;
}

.detail-icon {
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, #667eea, #764ba2);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
}

.detail-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.detail-label {
  font-size: 12px;
  font-weight: 600;
  color: #666;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.detail-value {
  font-size: 14px;
  font-weight: 500;
  color: #333;
}

/* Event actions */
.event-actions {
  display: flex;
  gap: 15px;
  padding: 20px 30px 30px;
  border-top: 1px solid rgba(0, 0, 0, 0.1);
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 20px;
  border: 2px solid;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  flex: 1;
  justify-content: center;
}

.edit-btn {
  background: rgba(102, 126, 234, 0.1);
  border-color: #667eea;
  color: #667eea;
}

.edit-btn:hover {
  background: #667eea;
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.delete-btn {
  background: rgba(255, 107, 107, 0.1);
  border-color: #ff6b6b;
  color: #ff6b6b;
}

.delete-btn:hover {
  background: #ff6b6b;
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(255, 107, 107, 0.3);
}

/* FullCalendar custom styles */
:deep(.fc) {
  font-family: inherit;
}

:deep(.fc-toolbar) {
  margin-bottom: 20px;
}

:deep(.fc-toolbar-title) {
  font-size: 24px;
  font-weight: 700;
  color: #333;
}

:deep(.fc-button) {
  background: linear-gradient(135deg, #667eea, #764ba2);
  border: none;
  border-radius: 10px;
  padding: 8px 16px;
  font-weight: 500;
  text-transform: none;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
  transition: all 0.3s ease;
}

:deep(.fc-button:hover) {
  transform: translateY(-2px);
  box-shadow: 0 6px 18px rgba(102, 126, 234, 0.3);
}

:deep(.fc-button:disabled) {
  opacity: 0.5;
  transform: none;
  box-shadow: none;
}

:deep(.fc-daygrid-day) {
  transition: all 0.3s ease;
}

:deep(.fc-daygrid-day:hover) {
  background: rgba(102, 126, 234, 0.05);
}

:deep(.fc-day-today) {
  background: rgba(102, 126, 234, 0.1) !important;
}

:deep(.fc-event) {
  border-radius: 8px;
  border: none;
  padding: 2px 6px;
  font-size: 12px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

:deep(.fc-event:hover) {
  transform: scale(1.02);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

:deep(.fc-more-link) {
  color: #667eea;
  font-weight: 500;
  font-size: 11px;
}

/* Responsive design */
@media (max-width: 768px) {
  .calendar-page {
    padding: 15px;
  }
  
  .header-section {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
  }
  
  .header-content {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
  }
  
  .animated-title {
    font-size: 24px;
  }
  
  .controls-section {
    width: 100%;
    justify-content: space-between;
  }
  
  .view-selector {
    flex: 1;
    justify-content: center;
  }
  
  .calendar-card {
    padding: 20px;
  }
  
  .stats-section {
    flex-direction: column;
  }
  
  .stat-card {
    min-width: auto;
  }
  
  .dialog-card {
    margin: 10px;
    max-width: calc(100vw - 20px);
  }
  
  .form-row {
    flex-direction: column;
  }
  
  .event-actions {
    flex-direction: column;
  }
  
  .type-selector {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 480px) {
  .header-content {
    align-items: center;
    text-align: center;
  }
  
  .icon-wrapper {
    width: 60px;
    height: 60px;
  }
  
  .icon-text {
    font-size: 24px;
  }
  
  .animated-title {
    font-size: 20px;
  }
  
  .view-btn {
    padding: 8px 12px;
    font-size: 12px;
  }
  
  .add-event-btn {
    padding: 10px 16px;
    font-size: 12px;
  }
}

/* Animation for entrance */
@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translate3d(0, 40px, 0);
  }
  to {
    opacity: 1;
    transform: translate3d(0, 0, 0);
  }
}
</style>