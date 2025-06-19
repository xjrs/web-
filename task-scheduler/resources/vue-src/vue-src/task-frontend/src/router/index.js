// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router'

// 按需导入页面组件
import HomePage from '../views/HomePage.vue'
import TaskPage from '../views/TaskPage.vue'
import CalendarPage from '../views/CalendarPage.vue'
import ProjectPage from '../views/ProjectPage.vue'
import StatisticsPage from '../views/StatisticsPage.vue'
import SettingsPage from '../views/SettingsPage.vue'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: HomePage
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
    component: SettingsPage
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

export default router
