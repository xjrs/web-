<script setup>
import { RouterLink, RouterView } from 'vue-router'
import { ref } from 'vue'

const isMobileMenuOpen = ref(false)

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value
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
            <li class="nav-item">
              <router-link to="/settings" class="nav-link">
                <span class="nav-icon">⚙️</span>
                <span class="nav-text">设置</span>
              </router-link>
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
          <li><router-link to="/settings" @click="isMobileMenuOpen = false">⚙️ 设置</router-link></li>
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