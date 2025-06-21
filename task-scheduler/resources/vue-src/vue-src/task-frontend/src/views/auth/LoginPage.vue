<template>
  <div class="auth-page">
    <div class="bg-decoration">
      <div class="floating-shape shape-1"></div>
      <div class="floating-shape shape-2"></div>
      <div class="floating-shape shape-3"></div>
    </div>

    <div class="auth-container">
      <div class="auth-card">
        <div class="header-section">
          <div class="main-icon">
            <div class="icon-wrapper">
              <span class="icon-text">🔐</span>
            </div>
          </div>
          <h2 class="animated-title">登录</h2>
          <p class="subtitle">欢迎回来！请登录您的账号</p>
        </div>

        <form @submit.prevent="handleLogin" class="auth-form">
          <div class="form-group">
            <label for="email">邮箱</label>
            <input
              type="email"
              id="email"
              v-model="email"
              required
              placeholder="请输入邮箱地址"
            />
          </div>

          <div class="form-group">
            <label for="password">密码</label>
            <input
              type="password"
              id="password"
              v-model="password"
              required
              placeholder="请输入密码"
            />
          </div>

          <div class="form-group checkbox-group">
            <label class="checkbox-label">
              <input type="checkbox" v-model="remember" />
              <span>记住我</span>
            </label>
            <router-link to="/auth/forgot-password" class="forgot-password">
              忘记密码？
            </router-link>
          </div>

          <div class="message" :class="{ error: isError }" v-if="message">
            {{ message }}
          </div>

          <button type="submit" class="submit-btn" :disabled="isLoading">
            {{ isLoading ? '登录中...' : '登录' }}
          </button>

          <div class="auth-links">
            <span>还没有账号？</span>
            <router-link to="/auth/register">立即注册</router-link>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from '../../utils/axios'

const router = useRouter()
const email = ref('')
const password = ref('')
const remember = ref(false)
const message = ref('')
const isError = ref(false)
const isLoading = ref(false)

const handleLogin = async () => {
  try {
    isLoading.value = true
    message.value = ''
    isError.value = false

    const response = await axios.post('/api/auth/login', {
      email: email.value,
      password: password.value,
      remember: remember.value
    })

    // 正常登录流程
    localStorage.setItem('token', response.data.access_token)
    localStorage.setItem('user', JSON.stringify(response.data.user))
    axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.access_token}`
    // 触发一个全局事件
    window.dispatchEvent(new Event('auth-state-changed'))
    // 获取重定向地址
    const redirectPath = router.currentRoute.value.query.redirect || '/'
    router.push(redirectPath)
  } catch (error) {
    isError.value = true
    if (error.response) {
      message.value = error.response.data.message || '登录失败，请检查您的登录信息'
    } else {
      message.value = '登录失败，请稍后重试'
    }
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
.auth-page {
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

.bg-decoration {
  position: absolute;
  width: 100%;
  height: 100%;
  overflow: hidden;
  z-index: 0;
}

.floating-shape {
  position: absolute;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 50%;
  animation: float 20s infinite linear;
}

.shape-1 {
  width: 300px;
  height: 300px;
  top: -150px;
  left: -150px;
}

.shape-2 {
  width: 200px;
  height: 200px;
  top: 50%;
  right: -100px;
  animation-delay: -7s;
}

.shape-3 {
  width: 150px;
  height: 150px;
  bottom: -75px;
  left: 50%;
  animation-delay: -14s;
}

@keyframes float {
  0% { transform: rotate(0deg) translate(0, 0); }
  50% { transform: rotate(180deg) translate(50px, 50px); }
  100% { transform: rotate(360deg) translate(0, 0); }
}

.auth-container {
  width: 100%;
  max-width: 480px;
  margin: 0 auto;
  position: relative;
  z-index: 1;
}

.auth-card {
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(10px);
  border-radius: 20px;
  padding: 40px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.header-section {
  text-align: center;
  margin-bottom: 30px;
}

.main-icon {
  margin-bottom: 20px;
}

.icon-wrapper {
  width: 80px;
  height: 80px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto;
}

.icon-text {
  font-size: 40px;
}

.animated-title {
  font-size: 32px;
  color: #2d3748;
  margin: 0;
  animation: slideInUp 0.5s ease;
}

.subtitle {
  color: #718096;
  margin-top: 8px;
}

.auth-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

label {
  color: #4a5568;
  font-weight: 500;
}

input {
  padding: 12px;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  font-size: 16px;
  transition: border-color 0.3s ease;
}

input:focus {
  outline: none;
  border-color: #667eea;
}

.checkbox-group {
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
}

.forgot-password {
  color: #667eea;
  text-decoration: none;
  font-weight: 500;
}

.forgot-password:hover {
  text-decoration: underline;
}

.message {
  padding: 12px;
  border-radius: 8px;
  background-color: #4299e1;
  color: white;
  text-align: center;
}

.message.error {
  background-color: #f56565;
}

.submit-btn {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 14px;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: transform 0.3s ease;
}

.submit-btn:hover {
  transform: translateY(-2px);
}

.submit-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

.auth-links {
  text-align: center;
  color: #4a5568;
}

.auth-links a {
  color: #667eea;
  text-decoration: none;
  font-weight: 500;
  margin-left: 4px;
}

.auth-links a:hover {
  text-decoration: underline;
}

@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@media (max-width: 480px) {
  .auth-container {
    width: 95%;
  }

  .auth-card {
    padding: 30px 20px;
  }

  .animated-title {
    font-size: 28px;
  }

  .icon-wrapper {
    width: 60px;
    height: 60px;
  }

  .icon-text {
    font-size: 30px;
  }
}
</style>