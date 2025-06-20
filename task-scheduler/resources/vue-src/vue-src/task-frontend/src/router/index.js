// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router'

// 按需导入页面组件
import HomePage from '../views/HomePage.vue'
import TaskPage from '../views/TaskPage.vue'
import CalendarPage from '../views/CalendarPage.vue'
import ProjectPage from '../views/ProjectPage.vue'
import StatisticsPage from '../views/StatisticsPage.vue'
import SettingsPage from '../views/SettingsPage.vue'

// 导入认证相关组件
import LoginPage from '../views/auth/LoginPage.vue'
import RegisterPage from '../views/auth/RegisterPage.vue'
import ForgotPasswordPage from '../views/auth/ForgotPasswordPage.vue'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: HomePage
  },
  // 认证路由
  {
    path: '/auth',
    children: [
      {
        path: 'login',
        name: 'Login',
        component: LoginPage
      },
      {
        path: 'register',
        name: 'Register',
        component: RegisterPage
      },
      {
        path: 'forgot-password',
        name: 'ForgotPassword',
        component: ForgotPasswordPage
      }
    ]
  },
  {
    path: '/tasks',
    name: 'Tasks',
    component: TaskPage
  },
  {
    path: '/calendar',
    name: 'Calendar',
    component: CalendarPage
  },
  {
    path: '/project',
    name: 'Project',
    component: ProjectPage
  },
  {
    path: '/statistics',
    name: 'Statistics',
    component: StatisticsPage
  },
  {
    path: '/settings',
    name: 'Settings',
    component: SettingsPage,
    meta: { requiresAuth: false }
  },
  // 404页面，可以做重定向或者展示 NotFound 组件
  {
    path: '/:pathMatch(.*)*',
    redirect: '/'
  }
]

const router = createRouter({
  history: createWebHistory(), // 使用 HTML5 History 模式
  routes
})

// 全局路由守卫
router.beforeEach((to, from, next) => {
  // 检查路由是否需要登录验证
  if (to.meta.requiresAuth) {
    const token = localStorage.getItem('token')
    if (!token) {
      // 如果未登录，重定向到登录页面
      next({
        path: '/auth/login',
        query: { redirect: to.fullPath }
      })
    } else {
      next()
    }
  } else {
    next()
  }
})

export default router
