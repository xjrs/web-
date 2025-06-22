<script setup>
import { RouterLink, RouterView } from 'vue-router'
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const isMobileMenuOpen = ref(false)
const isUserMenuOpen = ref(false)
const isLoggedIn = ref(false)
const userAvatar = ref('')

onMounted(() => { 
   checkLoginStatus()  // 初始检查
   // 监听 localStorage 变化（用于多标签页同步）
   window.addEventListener('storage', checkLoginStatus)
   // 监听自定义登录事件（用于同一页面内的状态更新）
   window.addEventListener('auth-state-changed', checkLoginStatus)
   // 监听全局点击事件，用于关闭用户菜单
   document.addEventListener('click', (event) => {
     // 如果点击的不是用户菜单区域，就关闭菜单
     if (!event.target.closest('.user-menu-container')) {
       isUserMenuOpen.value = false
     }
   })
})

const checkLoginStatus = () => {
  const token = localStorage.getItem('token')
  const user = JSON.parse(localStorage.getItem('user') || '{}')
  isLoggedIn.value = !!token
  userAvatar.value = user.avatar ? `http://localhost:8000/${user.avatar}` : ''
  isUserMenuOpen.value = false  // 确保菜单是关闭状态
}

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value
}

const toggleUserMenu = () => {
  isUserMenuOpen.value = !isUserMenuOpen.value
}

const handleLogin = () => {
  router.push('/auth/login')
}

const handleLogout = () => {
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  isLoggedIn.value = false
  userAvatar.value = ''
  router.push('/auth/login')
}
</script>

<template>
  <div id="app">
    <!-- Header with navigation -->
    <header class="app-header">
      <div class="header-container">
        <!-- Logo/Brand -->
        <div class="brand">
          <h1 class="brand-title">TaskFlow</h1>
          <span class="brand-subtitle">智能工作台</span>
        </div>

        <!-- Desktop Navigation -->
        <nav class="desktop-nav">
          <ul class="nav-list">
            <li class="nav-item">
              <router-link to="/" class="nav-link">
                <span class="nav-icon">🏠</span>
                <span class="nav-text">首页</span>
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/tasks" class="nav-link">
                <span class="nav-icon">✅</span>
                <span class="nav-text">任务管理</span>
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/calendar" class="nav-link">
                <span class="nav-icon">📅</span>
                <span class="nav-text">日程安排</span>
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/project" class="nav-link">
                <span class="nav-icon">📋</span>
                <span class="nav-text">项目总览</span>
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/statistics" class="nav-link">
                <span class="nav-icon">📊</span>
                <span class="nav-text">统计分析</span>
              </router-link>
            </li>
            <!-- User Avatar/Login Button -->
            <li class="nav-item user-menu-container">
              <template v-if="isLoggedIn">
                <button class="avatar-button" @click="toggleUserMenu">
                  <img v-if="userAvatar && userAvatar !== 'default-avatar.png'" :src="userAvatar" alt="User avatar" class="user-avatar" />
                  <div v-else class="default-avatar">
                    <img src="@/assets/default-avatar.svg" alt="Default Avatar" class="default-avatar-img" />
                  </div>
                </button>
                <div v-show="isUserMenuOpen" class="user-menu">
                  <router-link to="/settings" class="menu-item" @click="isUserMenuOpen = false">
                    <span class="menu-icon">⚙️</span>
                    <span>设置</span>
                  </router-link>
                  <button class="menu-item logout-button" @click="handleLogout">
                    <span class="menu-icon">🚪</span>
                    <span>退出登录</span>
                  </button>
                </div>
              </template>
              <button v-else class="login-button" @click="handleLogin">
                <span>登录</span>
              </button>
            </li>
          </ul>
        </nav>

        <!-- Mobile menu button -->
        <button class="mobile-menu-btn" @click="toggleMobileMenu">
          <span class="hamburger"></span>
        </button>
      </div>

      <!-- Mobile Navigation -->
      <nav class="mobile-nav" :class="{ 'is-open': isMobileMenuOpen }">
        <ul class="mobile-nav-list">
          <li><router-link to="/" @click="isMobileMenuOpen = false">🏠 首页</router-link></li>
          <li><router-link to="/tasks" @click="isMobileMenuOpen = false">✅ 任务管理</router-link></li>
          <li><router-link to="/calendar" @click="isMobileMenuOpen = false">📅 日程安排</router-link></li>
          <li><router-link to="/project" @click="isMobileMenuOpen = false">📋 项目总览</router-link></li>
          <li><router-link to="/statistics" @click="isMobileMenuOpen = false">📊 统计分析</router-link></li>

        </ul>
      </nav>
    </header>

    <!-- Main content area -->
    <main class="main-content">
      <div class="content-container">
        <router-view />
      </div>
    </main>
  </div>
</template>

<style>
/* Global styles */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

:root {
  --primary-color: #6366f1;
  --primary-hover: #5855eb;
  --secondary-color: #f1f5f9;
  --text-primary: #1e293b;
  --text-secondary: #64748b;
  --border-color: #e2e8f0;
  --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
  --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
  --border-radius: 8px;
  --transition: all 0.2s ease;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  color: var(--text-primary);
  line-height: 1.6;
}

#app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}
</style>

<style scoped>
/* Header styles */
.app-header {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border-bottom: 1px solid var(--border-color);
  position: sticky;
  top: 0;
  z-index: 100;
  box-shadow: var(--shadow-sm);
}

.header-container {
  max-width: 1400px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem 2rem;
}

/* Brand styles */
.brand {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.brand-title {
  font-size: 1.5rem;
  font-weight: 700;
  background: linear-gradient(135deg, var(--primary-color), #8b5cf6);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.brand-subtitle {
  font-size: 0.75rem;
  color: var(--text-secondary);
  font-weight: 500;
}

/* User menu styles */
.user-menu-container {
  position: relative;
  margin-left: 1rem;
}

.avatar-button {
  background: none;
  border: none;
  cursor: pointer;
  padding: 0;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-top: 4px;
}

.user-avatar {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.default-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.default-avatar-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 50%;
}

.login-button {
  background: linear-gradient(135deg, var(--primary-color), #8b5cf6);
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 25px;
  cursor: pointer;
  font-weight: 600;
  font-size: 0.9rem;
  letter-spacing: 0.5px;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
  position: relative;
  overflow: hidden;
  margin-top: 0.5rem;
}

.login-button::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.5s;
}

.login-button:hover {
  background: linear-gradient(135deg, var(--primary-hover), #7c3aed);
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
}

.login-button:hover::before {
  left: 100%;
}

.login-button:active {
  transform: translateY(0);
  box-shadow: 0 2px 10px rgba(99, 102, 241, 0.3);
}

.user-menu {
  position: absolute;
  top: 100%;
  right: 0;
  margin-top: 0.5rem;
  background: white;
  border-radius: var(--border-radius);
  box-shadow: var(--shadow-md);
  min-width: 200px;
  z-index: 1000;
}

.menu-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1rem;
  color: var(--text-primary);
  text-decoration: none;
  transition: var(--transition);
  cursor: pointer;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
  font-size: 1rem;
}

.menu-item:hover {
  background-color: var(--secondary-color);
}

.logout-button {
  color: #ef4444;
}

.menu-icon {
  font-size: 1.25rem;
}

/* Desktop navigation */
.desktop-nav {
  display: none;
}

.nav-list {
  display: flex;
  list-style: none;
  gap: 0.5rem;
}

.nav-item {
  position: relative;
}

.nav-link {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1rem;
  text-decoration: none;
  color: var(--text-secondary);
  border-radius: var(--border-radius);
  transition: var(--transition);
  font-weight: 500;
}

.nav-link:hover {
  background: var(--secondary-color);
  color: var(--primary-color);
  transform: translateY(-1px);
}

.nav-link.router-link-active {
  background: var(--primary-color);
  color: white;
  box-shadow: var(--shadow-md);
}

.nav-icon {
  font-size: 1.1rem;
}

.nav-text {
  font-size: 0.9rem;
}

/* Mobile menu button */
.mobile-menu-btn {
  display: flex;
  flex-direction: column;
  justify-content: center;
  width: 2.5rem;
  height: 2.5rem;
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: var(--border-radius);
  transition: var(--transition);
}

.mobile-menu-btn:hover {
  background: var(--secondary-color);
}

.hamburger {
  width: 1.5rem;
  height: 2px;
  background: var(--text-primary);
  position: relative;
  transition: var(--transition);
}

.hamburger::before,
.hamburger::after {
  content: '';
  position: absolute;
  width: 100%;
  height: 2px;
  background: var(--text-primary);
  transition: var(--transition);
}

.hamburger::before {
  top: -6px;
}

.hamburger::after {
  bottom: -6px;
}

/* Mobile navigation */
.mobile-nav {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: rgba(255, 255, 255, 0.98);
  backdrop-filter: blur(10px);
  border-bottom: 1px solid var(--border-color);
  transform: translateY(-100%);
  opacity: 0;
  visibility: hidden;
  transition: var(--transition);
}

.mobile-nav.is-open {
  transform: translateY(0);
  opacity: 1;
  visibility: visible;
}

.mobile-nav-list {
  list-style: none;
  padding: 1rem;
}

.mobile-nav-list li {
  margin-bottom: 0.5rem;
}

.mobile-nav-list a {
  display: block;
  padding: 1rem;
  text-decoration: none;
  color: var(--text-primary);
  border-radius: var(--border-radius);
  transition: var(--transition);
  font-weight: 500;
}

.mobile-nav-list a:hover,
.mobile-nav-list a.router-link-active {
  background: var(--primary-color);
  color: white;
}

/* Main content */
.main-content {
  flex: 1;
  padding: 2rem 0;
}

.content-container {
  max-width: 1700px;
  margin: 0 auto;
  padding: 0 3rem;
}

/* Responsive design */
@media (min-width: 768px) {
  .desktop-nav {
    display: block;
  }
  
  .mobile-menu-btn {
    display: none;
  }
  
  .mobile-nav {
    display: none;
  }
  
  .brand {
    flex-direction: row;
    align-items: baseline;
    gap: 0.75rem;
  }
  
  .brand-title {
    font-size: 1.75rem;
  }
  
  .brand-subtitle {
    font-size: 0.85rem;
  }
}

@media (min-width: 1024px) {
  .header-container {
    padding: 1.25rem 2rem;
  }
  
  .nav-link {
    padding: 0.875rem 1.25rem;
  }
  
  .nav-text {
    font-size: 0.95rem;
  }
}

/* Smooth animations */
@media (prefers-reduced-motion: reduce) {
  * {
    transition: none !important;
  }
}
</style>