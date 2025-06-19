<template>
    <div class="task-grid" style="width: 100%; height: 400px;" ref="gridContainer"></div>
  </template>
  
  <script>
 import { Grid } from "dhtmlx-suite";
 import "dhtmlx-suite/codebase/dhtmlx.css"; // 引入样式
  
  export default {
    name: 'TaskGrid',
    data() {
      return {
        grid: null
      }
    },
    mounted() {
      this.initGrid()
      this.loadData()
    },
    beforeUnmount() {
      if (this.grid) {
        this.grid.destructor()
        this.grid = null
      }
    },
    methods: {
      initGrid() {
        this.grid = new Grid(this.$refs.gridContainer, {
          columns: [
            { id: 'id', header: [{ text: 'ID' }], width: 60, sort: 'int' },
            { id: 'name', header: [{ text: '任务名称' }], fillspace: true, sort: 'string' },
            { id: 'due_date', header: [{ text: '截止日期' }], width: 150, sort: 'string' },
            { id: 'status', header: [{ text: '状态' }], width: 100, sort: 'string' },
            { id: 'priority', header: [{ text: '优先级' }], width: 100, sort: 'string' }
          ],
          autoWidth: true,
          multiselect: true,
          editable: false,
          resizable: true
        })
      },
      loadData() {
        fetch('/api/tasks')
          .then(res => {
            if (!res.ok) throw new Error('网络响应错误')
            return res.json()
          })
          .then(data => {
            // 这里假设后端返回的是任务数组，字段对应 grid 的列 id
            this.grid.data.parse(data)
          })
          .catch(err => {
            console.error('加载任务数据失败:', err)
          })
      }
    }
  }
  </script>
  
  <style scoped>
  .task-grid {
    border: 1px solid #ccc;
    border-radius: 4px;
  }
  </style>
  