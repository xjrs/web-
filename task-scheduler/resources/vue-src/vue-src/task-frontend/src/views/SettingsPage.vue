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
        <div class="header-text">
          <h2 class="animated-title">个人设置</h2>
          <p class="subtitle">管理您的个人信息和偏好设置</p>
        </div>
      </div>

      <div class="settings-card">
        <form @submit.prevent="saveSettings" class="settings-form">
          <div class="form-section">
            <div class="section-title">
              <span class="section-icon">👤</span>
              <span>个人信息</span>
            </div>
            
            <div class="form-group">
              <label for="username">
                <span class="label-icon">👤</span>
                用户名
              </label>
              <input 
                id="username" 
                v-model="userInfo.username" 
                type="text" 
                placeholder="请输入用户名"
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
                v-model="userInfo.email" 
                type="email" 
                placeholder="邮箱地址"
                class="input-field" 
                readonly
                title="邮箱不可编辑"
              />
            </div>

            <div class="form-group">
              <label for="current-password">
                <span class="label-icon">🔐</span>
                当前密码
              </label>
              <div class="password-input-wrapper">
                <input 
                  id="current-password" 
                  v-model="userInfo.currentPassword" 
                  :type="showCurrentPassword ? 'text' : 'password'" 
                  placeholder="请输入当前密码"
                  class="input-field" 
                />
                <button 
                  type="button" 
                  @click="toggleCurrentPassword" 
                  class="password-toggle"
                >
                  {{ showCurrentPassword ? '🙈' : '👁️' }}
                </button>
              </div>
            </div>

            <div class="form-group">
              <label for="password">
                <span class="label-icon">🔒</span>
                新密码
              </label>
              <div class="password-input-wrapper">
                <input 
                  id="password" 
                  v-model="userInfo.password" 
                  :type="showPassword ? 'text' : 'password'" 
                  placeholder="请输入新密码"
                  class="input-field" 
                  :disabled="!userInfo.currentPassword.trim()"
                />
                <button 
                  type="button" 
                  @click="togglePassword" 
                  class="password-toggle"
                  :disabled="!userInfo.currentPassword.trim()"
                >
                  {{ showPassword ? '🙈' : '👁️' }}
                </button>
              </div>
              <small class="field-hint" v-if="!userInfo.currentPassword.trim()">
                请先输入当前密码才能修改新密码
              </small>
            </div>

            <div class="form-group" v-if="userInfo.password.trim()">
              <label for="password-confirmation">
                <span class="label-icon">🔒</span>
                确认新密码
              </label>
              <div class="password-input-wrapper">
                <input 
                  id="password-confirmation" 
                  v-model="userInfo.passwordConfirmation" 
                  :type="showPasswordConfirmation ? 'text' : 'password'" 
                  placeholder="请再次输入新密码"
                  class="input-field" 
                />
                <button 
                  type="button" 
                  @click="togglePasswordConfirmation" 
                  class="password-toggle"
                >
                  {{ showPasswordConfirmation ? '🙈' : '👁️' }}
                </button>
              </div>
              <small class="field-hint" v-if="userInfo.password !== userInfo.passwordConfirmation && userInfo.passwordConfirmation">
                两次输入的密码不一致
              </small>
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
            
            <div class="form-group">
              <div class="checkbox-wrapper">
                <label class="checkbox-label">
                  <input type="checkbox" v-model="userInfo.emailNotifications">
                  <span>邮件通知</span>
                </label>
              </div>
            </div>

            <div class="form-group">
              <div class="checkbox-wrapper">
                <label class="checkbox-label">
                  <input type="checkbox" v-model="userInfo.pushNotifications">
                  <span>推送通知</span>
                </label>
              </div>
            </div>
          </div>

          <div class="form-actions">
            <button @click="saveSettings" class="save-btn" :disabled="loading">
              <span v-if="loading">保存中...</span>
              <span v-else>保存设置</span>
            </button>
          </div>
        </form>

        <transition name="fade">
          <div v-if="message" :class="['message', messageType]">
            <i :class="messageType === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle'"></i>
            {{ message }}
          </div>
        </transition>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from '@/utils/axios'

export default {
  name: 'SettingsPage',
  setup() {
    const router = useRouter()
    
    // 响应式数据
    const userInfo = ref({
      username: '',
      email: '',
      currentPassword: '',
      password: '',
      passwordConfirmation: ''
    })
    const theme = ref('light')
    const language = ref('zh-CN')
    const notifications = ref({
      email: true,
      browser: false
    })
    const showCurrentPassword = ref(false)
    const showPassword = ref(false)
    const showPasswordConfirmation = ref(false)
    const message = ref('')
    const messageType = ref('success')
    const loading = ref(false)
    
    // 检查登录状态
    const checkLoginStatus = () => {
      const token = localStorage.getItem('token')
      const user = localStorage.getItem('user')
      
      if (!token || !user) {
        router.push('/login')
        return false
      }
      
      try {
        const userData = JSON.parse(user)
        userInfo.value.username = userData.name || ''
        userInfo.value.email = userData.email || ''
        return true
      } catch (error) {
        console.error('解析用户数据失败:', error)
        router.push('/login')
        return false
      }
    }
    
    // 切换密码显示
    const toggleCurrentPassword = () => {
      showCurrentPassword.value = !showCurrentPassword.value
    }
    
    const togglePassword = () => {
      showPassword.value = !showPassword.value
    }
    
    const togglePasswordConfirmation = () => {
      showPasswordConfirmation.value = !showPasswordConfirmation.value
    }
    
    // 保存设置
    const saveSettings = async () => {
      loading.value = true
      
      // 验证密码相关输入
      if (userInfo.value.password.trim()) {
        if (!userInfo.value.currentPassword.trim()) {
          message.value = '请输入当前密码'
          messageType.value = 'error'
          loading.value = false
          setTimeout(() => {
            message.value = ''
          }, 3000)
          return
        }
        
        if (userInfo.value.password !== userInfo.value.passwordConfirmation) {
          message.value = '两次输入的新密码不一致'
          messageType.value = 'error'
          loading.value = false
          setTimeout(() => {
            message.value = ''
          }, 3000)
          return
        }
        
        if (userInfo.value.password.length < 3) {
          message.value = '新密码长度至少为3位'
          messageType.value = 'error'
          loading.value = false
          setTimeout(() => {
            message.value = ''
          }, 3000)
          return
        }
      }
      
      try {
        const token = localStorage.getItem('token')
        const updateData = {
          name: userInfo.value.username
        }
        
        // 如果密码不为空，则包含密码更新
        if (userInfo.value.password.trim()) {
          updateData.current_password = userInfo.value.currentPassword
          updateData.password = userInfo.value.password
          updateData.password_confirmation = userInfo.value.passwordConfirmation
        }
        
        const response = await axios.put('/api/user/update', updateData)
        
        if (response.data) {
          
          // 更新本地存储的用户信息
          const currentUser = JSON.parse(localStorage.getItem('user'))
          currentUser.name = userInfo.value.username
          localStorage.setItem('user', JSON.stringify(currentUser))
          
          // 清空密码相关字段
          userInfo.value.currentPassword = ''
          userInfo.value.password = ''
          userInfo.value.passwordConfirmation = ''
          
          message.value = '设置已保存！'
          messageType.value = 'success'
          setTimeout(() => {
            message.value = ''
          }, 3000)
        }
      } catch (error) {
        console.error('保存设置失败:', error)
        message.value = error.response?.data?.errors?.current_password?.[0] || error.response?.data?.errors?.password?.[0] || error.response?.data?.message || '未知错误，请重试'
        messageType.value = 'error'
        setTimeout(() => {
          message.value = ''
        }, 3000)
      } finally {
        loading.value = false
      }
    }
    
    onMounted(() => {
      // 检查登录状态
      if (!checkLoginStatus()) {
        return
      }
      
      // 添加入场动画
      setTimeout(() => {
        const elements = document.querySelectorAll('.settings-card, .header-section')
        elements.forEach((el, index) => {
          setTimeout(() => {
            el.style.opacity = '1'
            el.style.transform = 'translateY(0)'
          }, index * 100)
        })
      }, 100)
    })
    
    return {
      userInfo,
      theme,
      language,
      notifications,
      showCurrentPassword,
      showPassword,
      showPasswordConfirmation,
      message,
      messageType,
      loading,
      toggleCurrentPassword,
      togglePassword,
      togglePasswordConfirmation,
      saveSettings
    }
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
  display: flex;
  align-items: center;
  gap: 24px;
  margin-bottom: 40px;
  padding: 0 20px;
}

.main-icon {
  flex-shrink: 0;
}

.icon-wrapper {
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

.header-text {
  flex: 1;
  text-align: left;
}

.animated-title {
  margin-bottom: 8px;
  font-size: 38px;
  font-weight: 800;
  font-family: 'PingFang SC', 'Microsoft YaHei', 'Helvetica Neue', Arial, sans-serif;
  color: white;
  letter-spacing: 1px;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
  position: relative;
  line-height: 1.2;
}



.subtitle {
  color: #ffffff;
  font-size: 18px;
  margin-bottom: 0;
  opacity: 0.9;
  text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
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
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
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

.input-field[readonly] {
  background: rgba(248, 249, 250, 0.8);
  color: #6c757d;
  cursor: not-allowed;
}

.password-input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.password-input-wrapper .input-field {
  padding-right: 50px;
}

.password-input-wrapper .input-field:disabled {
  background-color: #f8f9fa;
  color: #6c757d;
  cursor: not-allowed;
  opacity: 0.6;
}

.password-toggle {
  position: absolute;
  right: 12px;
  background: none;
  border: none;
  font-size: 18px;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: background-color 0.2s ease;
}

.password-toggle:hover {
  background-color: rgba(0, 0, 0, 0.05);
}

.password-toggle:disabled {
  color: #ccc;
  cursor: not-allowed;
}

.password-mismatch {
  color: #dc3545;
  font-size: 12px;
  margin-top: 5px;
  display: block;
}

/* Checkbox styles */
.checkbox-group {
  margin-bottom: 12px;
}

.checkbox-wrapper {
  width: 100%;
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
  width: 100%;
  box-sizing: border-box;
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

.form-actions {
  display: flex;
  justify-content: flex-end;
  margin-top: 24px;
}

.save-btn {
  padding: 18px 36px;
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
  min-width: 140px;
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

.save-btn:disabled {
  background: linear-gradient(135deg, #ccc, #999);
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

.save-btn:disabled:hover {
  transform: none;
  box-shadow: none;
}

.btn-icon {
  font-size: 20px;
}

.message {
  margin-top: 24px;
  padding: 16px 20px;
  border-radius: 15px;
  text-align: center;
  font-size: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  backdrop-filter: blur(10px);
}

.message.success {
  background: linear-gradient(135deg, #f0f9eb, #e8f5e8);
  color: #67c23a;
  border: 1px solid rgba(103, 194, 58, 0.2);
  box-shadow: 0 4px 15px rgba(103, 194, 58, 0.1);
}

.message.error {
  background: linear-gradient(135deg, #fef0f0, #fde2e2);
  color: #f56565;
  border: 1px solid rgba(245, 101, 101, 0.2);
  box-shadow: 0 4px 15px rgba(245, 101, 101, 0.1);
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