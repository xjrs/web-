<template>
  <div class="settings-container">
    <h2>设置页面</h2>
    <form @submit.prevent="saveSettings" class="settings-form">
      <div class="form-group">
        <label for="nickname">昵称</label>
        <input id="nickname" v-model="nickname" type="text" placeholder="请输入昵称" />
      </div>
      <div class="form-group">
        <label for="email">邮箱</label>
        <input id="email" v-model="email" type="email" placeholder="请输入邮箱" />
      </div>
      <div class="form-group">
        <label>主题</label>
        <select v-model="theme">
          <option value="light">浅色</option>
          <option value="dark">深色</option>
        </select>
      </div>
      <button type="submit" class="save-btn">保存设置</button>
    </form>
    <div v-if="message" class="message">{{ message }}</div>
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
      message: ""
    }
  },
  methods: {
    saveSettings() {
      // 这里可以添加实际的保存逻辑，如调用API
      this.message = "设置已保存！"
      setTimeout(() => {
        this.message = ""
      }, 2000)
    }
  }
}
</script>

<style scoped>
.settings-container {
  max-width: 400px;
  margin: 40px auto;
  padding: 32px 24px;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}
h2 {
  text-align: center;
  margin-bottom: 24px;
  color: #333;
}
.settings-form {
  display: flex;
  flex-direction: column;
  gap: 18px;
}
.form-group {
  display: flex;
  flex-direction: column;
}
label {
  margin-bottom: 6px;
  color: #555;
  font-size: 15px;
}
input, select {
  padding: 8px 10px;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 15px;
}
.save-btn {
  margin-top: 10px;
  padding: 10px 0;
  background: #409eff;
  color: #fff;
  border: none;
  border-radius: 6px;
  font-size: 16px;
  cursor: pointer;
  transition: background 0.2s;
}
.save-btn:hover {
  background: #307ae8;
}
.message {
  margin-top: 18px;
  text-align: center;
  color: #52c41a;
  font-size: 15px;
}
</style>