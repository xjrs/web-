import axios from 'axios'

// 创建axios实例
const instance = axios.create({
  baseURL: 'http://localhost:8000',
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

// 请求拦截器
instance.interceptors.request.use(
  config => {
    const token = localStorage.getItem('token')
    if (token) {
      config.headers['Authorization'] = `Bearer ${token}`
    }
    return config
  },
  error => {
    return Promise.reject(error)
  }
)

// 响应拦截器
instance.interceptors.response.use(
  response => {
    return response
  },
  error => {
    if (error.response) {
      // 处理401未授权错误
      if (error.response.status === 401) {
        // 清除token和用户信息
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        // 检查当前是否在登录页面
        if (!window.location.pathname.includes('/auth/login')) {
          window.location.href = '/auth/login'
        }
        return Promise.reject(error)
      }
    }
    return Promise.reject(error)
  }
)

export default instance