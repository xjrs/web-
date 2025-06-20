<template>
  <div class="settings-page">
    <!-- Animated background elements -->
    <div class="bg-decoration">
      <div class="floating-shape shape-1"></div>
      <div class="floating-shape shape-2"></div>
      <div class="floating-shape shape-3"></div>
    </div>

    <div class="settings-container">
      <div class="header-section">
        <div class="main-icon">
          <div class="icon-wrapper">
            <span class="icon-text">⚙️</span>
          </div>
        </div>
        <h2 class="animated-title">个人设置</h2>
        <p class="subtitle">自定义您的个人偏好设置</p>
      </div>

      <div class="settings-card">
        <form @submit.prevent="saveSettings" class="settings-form">
          <div class="form-section">
            <div class="section-title">
              <span class="section-icon">👤</span>
              <span>个人信息</span>
            </div>
            
            <div class="form-group">
              <label for="nickname">
                <span class="label-icon">🏷️</span>
                昵称
              </label>
              <input 
                id="nickname" 
                v-model="nickname" 
                type="text" 
                placeholder="请输入昵称"
                class="input-field" 
              />
            </div>

            <div class="form-group">
              <label for="email">
                <span class="label-icon">📧</span>
                邮箱
              </label>
              <input 
                id="email" 
                v-model="email" 
                type="email" 
                placeholder="请输入邮箱"
                class="input-field" 
              />
            </div>
          </div>

          <div class="form-section">
            <div class="section-title">
              <span class="section-icon">🎨</span>
              <span>外观设置</span>
            </div>
            
            <div class="form-group">
              <label for="theme">
                <span class="label-icon">🌈</span>
                主题
              </label>
              <select 
                id="theme"
                v-model="theme"
                class="select-field"
              >
                <option value="light">浅色主题</option>
                <option value="dark">深色主题</option>
                <option value="auto">跟随系统</option>
              </select>
            </div>

            <div class="form-group">
              <label for="language">
                <span class="label-icon">🌍</span>
                语言
              </label>
              <select 
                id="language"
                v-model="language"
                class="select-field"
              >
                <option value="zh-CN">简体中文</option>
                <option value="en-US">English</option>
              </select>
            </div>
          </div>

          <div class="form-section">
            <div class="section-title">
              <span class="section-icon">🔔</span>
              <span>通知设置</span>
            </div>
            
            <div class="form-group checkbox-group">
              <label class="checkbox-label">
                <input 
                  type="checkbox" 
                  v-model="notifications.email"
                  class="checkbox-input"
                />
                <span class="checkbox-custom"></span>
                <span class="checkbox-text">
                  <span class="label-icon">📬</span>
                  邮件通知
                </span>
              </label>
            </div>

            <div class="form-group checkbox-group">
              <label class="checkbox-label">
                <input 
                  type="checkbox" 
                  v-model="notifications.browser"
                  class="checkbox-input"
                />
                <span class="checkbox-custom"></span>
                <span class="checkbox-text">
                  <span class="label-icon">🔔</span>
                  浏览器通知
                </span>
              </label>
            </div>
          </div>

          <button type="submit" class="save-btn">
            <span class="btn-icon">💾</span>
            保存设置
          </button>
        </form>

        <transition name="fade">
          <div v-if="message" class="message">
            <i class="fas fa-check-circle"></i>
            {{ message }}
          </div>
        </transition>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "SettingsPage",
  data() {
    return {
      nickname: "",
      email: "",
      theme: "light",
      language: "zh-CN",
      notifications: {
        email: true,
        browser: false
      },
      message: ""
    }
  },
  methods: {
    async saveSettings() {
      try {
        // 模拟API调用
        await new Promise(resolve => setTimeout(resolve, 500));
        this.message = "设置已成功保存！";
        setTimeout(() => {
          this.message = "";
        }, 3000);
      } catch (error) {
        console.error('保存设置失败:', error);
        this.message = "保存失败，请稍后重试";
      }
    }
  },
  mounted() {
    // Add entrance animation
    this.$nextTick(() => {
      const card = document.querySelector('.settings-card');
      if (card) {
        card.style.animation = 'slideInUp 0.8s ease-out';
      }
    });
    // 这里可以添加初始化逻辑，如从服务器获取用户设置
  }
}
</script>

<style scoped>
.settings-page {
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

.settings-container {
  max-width: 800px;
  width: 90%;
  margin: 0 auto;
}

.header-section {
  text-align: center;
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

.settings-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  padding: 50px;
  border-radius: 30px;
  box-shadow: 
    0 20px 40px rgba(0, 0, 0, 0.1),
    0 10px 20px rgba(0, 0, 0, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.settings-form {
  display: flex;
  flex-direction: column;
  gap: 32px;
}

.form-section {
  background: rgba(255, 255, 255, 0.5);
  border-radius: 20px;
  padding: 24px;
  border: 1px solid rgba(255, 255, 255, 0.3);
  backdrop-filter: blur(10px);
}

.section-title {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 20px;
  font-weight: 600;
  color: #333;
  margin-bottom: 20px;
  padding-bottom: 12px;
  border-bottom: 2px solid rgba(102, 126, 234, 0.2);
}

.section-icon {
  font-size: 24px;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea, #764ba2);
  border-radius: 12px;
  color: white;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-bottom: 16px;
}

.form-group:last-child {
  margin-bottom: 0;
}

label {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #333;
  font-size: 16px;
  font-weight: 500;
}

.label-icon {
  font-size: 16px;
  width: 24px;
  text-align: center;
}

.input-field,
.select-field {
  padding: 16px 20px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: 15px;
  font-size: 16px;
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(10px);
  transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}

.input-field:focus,
.select-field:focus {
  border-color: rgba(102, 126, 234, 0.5);
  box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
  outline: none;
  transform: translateY(-2px);
  background: rgba(255, 255, 255, 0.95);
}

/* Checkbox styles */
.checkbox-group {
  margin-bottom: 12px;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 12px;
  cursor: pointer;
  padding: 12px 16px;
  border-radius: 12px;
  transition: all 0.3s ease;
  background: rgba(255, 255, 255, 0.3);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.checkbox-label:hover {
  background: rgba(255, 255, 255, 0.5);
  transform: translateY(-1px);
}

.checkbox-input {
  display: none;
}

.checkbox-custom {
  width: 20px;
  height: 20px;
  border: 2px solid #667eea;
  border-radius: 6px;
  position: relative;
  transition: all 0.3s ease;
  background: white;
}

.checkbox-input:checked + .checkbox-custom {
  background: linear-gradient(135deg, #667eea, #764ba2);
  border-color: #667eea;
}

.checkbox-input:checked + .checkbox-custom::after {
  content: '✓';
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: white;
  font-size: 12px;
  font-weight: bold;
}

.checkbox-text {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 16px;
  color: #333;
}

.save-btn {
  margin-top: 24px;
  padding: 18px 0;
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: #fff;
  border: none;
  border-radius: 20px;
  font-size: 18px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
  position: relative;
  overflow: hidden;
}

.save-btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.5s;
}

.save-btn:hover::before {
  left: 100%;
}

.save-btn:hover {
  transform: translateY(-3px) scale(1.02);
  box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
}

.save-btn:active {
  transform: translateY(-1px) scale(1.01);
}

.btn-icon {
  font-size: 20px;
}

.message {
  margin-top: 24px;
  padding: 16px 20px;
  background: linear-gradient(135deg, #f0f9eb, #e8f5e8);
  border-radius: 15px;
  text-align: center;
  color: #67c23a;
  font-size: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  border: 1px solid rgba(103, 194, 58, 0.2);
  box-shadow: 0 4px 15px rgba(103, 194, 58, 0.1);
  backdrop-filter: blur(10px);
}

.message i {
  font-size: 20px;
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

.fade-enter-active,
.fade-leave-active {
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.fade-enter-from {
  opacity: 0;
  transform: translateY(20px) scale(0.95);
}

.fade-leave-to {
  opacity: 0;
  transform: translateY(-20px) scale(0.95);
}

/* 响应式设计 */
@media (max-width: 1024px) {
  .settings-container {
    max-width: 90%;
  }
}

@media (max-width: 768px) {
  .settings-page {
    padding: 15px;
  }

  .settings-container {
    width: 95%;
  }

  .settings-card {
    padding: 30px 25px;
  }

  .animated-title {
    font-size: 28px;
  }

  .subtitle {
    font-size: 16px;
  }

  .form-section {
    padding: 20px;
  }

  .section-title {
    font-size: 18px;
  }

  .section-icon {
    width: 35px;
    height: 35px;
    font-size: 20px;
  }
}

@media (max-width: 480px) {
  .settings-card {
    padding: 25px 20px;
    margin: 10px;
  }

  .animated-title {
    font-size: 24px;
  }

  .subtitle {
    font-size: 14px;
  }

  .form-section {
    padding: 16px;
  }

  .section-title {
    font-size: 16px;
    gap: 8px;
  }

  .section-icon {
    width: 30px;
    height: 30px;
    font-size: 18px;
  }

  .input-field,
  .select-field {
    padding: 14px 16px;
    font-size: 14px;
  }

  .save-btn {
    padding: 16px 0;
    font-size: 16px;
  }

  .icon-wrapper {
    width: 60px;
    height: 60px;
  }

  .icon-text {
    font-size: 24px;
  }
}
</style>