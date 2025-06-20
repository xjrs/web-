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
        // 在新窗口打开登录页面
        const loginWindow = window.open('/auth/login', '_blank', 'width=500,height=600')
        
        // 监听登录成功消息
        window.addEventListener('message', function(event) {
          if (event.data.type === 'login-success') {
            // 保存新的token
            localStorage.setItem('token', event.data.token)
            localStorage.setItem('user', JSON.stringify(event.data.user))
            // 关闭登录窗口
            loginWindow.close()
            // 重新发送之前失败的请求
            return instance(error.config)
          }
        })
      }
    }
    return Promise.reject(error)
  }
)

export default instance