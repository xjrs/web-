import './assets/main.css'

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import axios from './utils/axios'

// 创建Vue应用实例
const app = createApp(App)

// 配置全局属性
app.config.globalProperties.$axios = axios

// 使用路由
app.use(router)

// 检查用户登录状态
const token = localStorage.getItem('token')
if (token) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
}

// 挂载应用
app.mount('#app')
