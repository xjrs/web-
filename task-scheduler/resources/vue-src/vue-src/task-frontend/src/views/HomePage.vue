<template>
  <div class="home-page">
    <!-- Animated background elements -->
    <div class="bg-decoration">
      <div class="floating-shape shape-1"></div>
      <div class="floating-shape shape-2"></div>
      <div class="floating-shape shape-3"></div>
    </div>

    <div class="card">
      <!-- Header with animated icon -->
      <div class="header-section">
        <div class="main-icon">
          <div class="icon-wrapper">
            <span class="icon-text">🚀</span>
          </div>
        </div>
        <h1 class="animated-title">欢迎来到任务管理系统</h1>
        <p class="subtitle">高效管理，轻松完成每一个目标</p>
      </div>

      <!-- Feature cards -->
      <div class="nav-buttons">
        <router-link to="/tasks" class="nav-button tasks-btn">
          <div class="button-content">
            <div class="button-icon">📋</div>
            <div class="button-text">
              <span class="button-title">任务管理</span>
              <span class="button-desc">创建和跟踪任务</span>
            </div>
          </div>
        </router-link>

        <router-link to="/calendar" class="nav-button calendar-btn">
          <div class="button-content">
            <div class="button-icon">📅</div>
            <div class="button-text">
              <span class="button-title">日程安排</span>
              <span class="button-desc">规划您的时间</span>
            </div>
          </div>
        </router-link>

        <router-link to="/statistics" class="nav-button stats-btn">
          <div class="button-content">
            <div class="button-icon">📊</div>
            <div class="button-text">
              <span class="button-title">数据统计</span>
              <span class="button-desc">分析您的表现</span>
            </div>
          </div>
        </router-link>
      </div>

      <!-- Quick stats or motivational message -->
      <div class="footer-section">
        <p class="motivational-text">✨ 今天也要加油哦！</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "HomePage",
  mounted() {
    // Add entrance animation
    this.$nextTick(() => {
      const card = document.querySelector('.card');
      card.style.animation = 'slideInUp 0.8s ease-out';
    });
  }
};
</script>

<style scoped>
.home-page {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
  padding: 20px;
  position: relative;
  overflow: hidden;
}

/* Animated background */
.bg-decoration {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
}

.floating-shape {
  position: absolute;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 50%;
  animation: float 6s ease-in-out infinite;
}

.shape-1 {
  width: 80px;
  height: 80px;
  top: 20%;
  left: 10%;
  animation-delay: 0s;
}

.shape-2 {
  width: 120px;
  height: 120px;
  top: 60%;
  right: 15%;
  animation-delay: 2s;
}

.shape-3 {
  width: 60px;
  height: 60px;
  bottom: 20%;
  left: 20%;
  animation-delay: 4s;
}

@keyframes float {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  50% { transform: translateY(-20px) rotate(180deg); }
}

/* Main card */
.card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  padding: 60px 50px;
  border-radius: 30px;
  box-shadow: 
    0 20px 40px rgba(0, 0, 0, 0.1),
    0 10px 20px rgba(0, 0, 0, 0.05);
  text-align: center;
  max-width: 1000px;
  width: 90%;
  border: 1px solid rgba(255, 255, 255, 0.2);
}

/* Header section */
.header-section {
  margin-bottom: 40px;
}

.main-icon {
  margin-bottom: 20px;
}

.icon-wrapper {
  display: inline-block;
  width: 80px;
  height: 80px;
  background: linear-gradient(135deg, #ff6b6b, #ffa726);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  animation: pulse 2s infinite;
  box-shadow: 0 10px 30px rgba(255, 107, 107, 0.3);
}

.icon-text {
  font-size: 32px;
  animation: bounce 2s infinite;
}

@keyframes pulse {
  0% { transform: scale(1); }
  50% { transform: scale(1.05); }
  100% { transform: scale(1); }
}

@keyframes bounce {
  0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
  40% { transform: translateY(-5px); }
  60% { transform: translateY(-3px); }
}

.animated-title {
  margin-bottom: 16px;
  font-size: 36px;
  color: #333;
  font-weight: 700;
  background: linear-gradient(135deg, #667eea, #764ba2);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.subtitle {
  color: #666;
  font-size: 18px;
  margin-bottom: 0;
  opacity: 0.8;
}

/* Navigation buttons */
.nav-buttons {
  display: flex;
  flex-direction: row;
  gap: 24px;
  margin-bottom: 40px;
  justify-content: center;
}

.nav-button {
  padding: 0;
  border-radius: 20px;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  position: relative;
  overflow: hidden;
  flex: 1;
  max-width: 280px;
}

.nav-button::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.5s;
}

.nav-button:hover::before {
  left: 100%;
}

.button-content {
  padding: 30px 20px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16px;
  text-align: center;
  min-height: 120px;
}

.button-icon {
  font-size: 32px;
  width: 60px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 15px;
  flex-shrink: 0;
}

.button-text {
  display: flex;
  flex-direction: column;
  gap: 6px;
  align-items: center;
}

.button-title {
  font-size: 18px;
  font-weight: 600;
  color: white;
}

.button-desc {
  font-size: 14px;
  opacity: 0.9;
  color: rgba(255, 255, 255, 0.8);
}

/* Button specific colors */
.tasks-btn {
  background: linear-gradient(135deg, #667eea, #764ba2);
  box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.calendar-btn {
  background: linear-gradient(135deg, #f093fb, #f5576c);
  box-shadow: 0 8px 25px rgba(240, 147, 251, 0.3);
}

.stats-btn {
  background: linear-gradient(135deg, #4facfe, #00f2fe);
  box-shadow: 0 8px 25px rgba(79, 172, 254, 0.3);
}

.nav-button:hover {
  transform: translateY(-8px) scale(1.02);
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
}

/* Footer section */
.footer-section {
  border-top: 1px solid rgba(0, 0, 0, 0.1);
  padding-top: 20px;
}

.motivational-text {
  color: #888;
  font-size: 14px;
  margin: 0;
  font-style: italic;
}

/* Responsive design */
@media (max-width: 1024px) {
  .card {
    max-width: 800px;
  }
}

@media (max-width: 768px) {
  .nav-buttons {
    flex-direction: column;
    gap: 16px;
  }

  .nav-button {
    max-width: none;
  }

  .button-content {
    flex-direction: row;
    text-align: left;
    padding: 20px;
    min-height: auto;
  }

  .button-icon {
    font-size: 24px;
    width: 50px;
    height: 50px;
  }

  .button-text {
    align-items: flex-start;
    gap: 4px;
  }

  .button-title {
    font-size: 16px;
  }

  .button-desc {
    font-size: 12px;
  }
}

@media (max-width: 480px) {
  .card {
    padding: 30px 20px;
    margin: 10px;
    width: 95%;
  }

  .animated-title {
    font-size: 24px;
  }

  .subtitle {
    font-size: 16px;
  }

  .button-content {
    padding: 16px;
  }
}

/* Entrance animation */
@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(50px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>