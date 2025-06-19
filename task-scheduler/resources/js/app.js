import { createApp } from 'vue';
import axios from 'axios';
import 'dhtmlx-suite/codebase/dhtmlx.css'; // 引入样式
import { Grid } from 'dhtmlx-suite';

// 定义一个Vue组件，用于展示任务表格
const TaskGrid = {
  template: `<div id="task_grid" style="width:100%; height:400px;"></div>`,
  data() {
    return {
      grid: null,
      tasks: [],
    };
  },
  mounted() {
    this.loadTasks();
  },
  methods: {
    loadTasks() {
      axios.get('/api/tasks').then(response => {
        this.tasks = response.data.data || response.data; // 兼容分页或非分页数据
        this.initGrid();
      }).catch(e => {
        console.error('加载任务失败', e);
      });
    },
    initGrid() {
      if(this.grid) this.grid.destructor(); // 先销毁已有实例
      this.grid = new Grid(null, {
        columns: [
          { id: "id", header: [{ text: "ID" }], width: 50 },
          { id: "title", header: [{ text: "标题" }], width: 200, editable: true },
          { id: "status", header: [{ text: "状态" }], width: 120, editable: true },
          { id: "due_date", header: [{ text: "截止日期" }], width: 150, editable: true },
        ],
        data: this.tasks,
        editable: true,
        autoWidth: true,
        autoHeight: true,
      });
      this.grid.render(document.getElementById('task_grid'));
      // 监听编辑事件，调用更新接口（可以扩展）
      this.grid.events.on('afterEditStop', (rowId, columnId, newValue) => {
        const task = this.tasks.find(t => t.id == rowId);
        if (!task) return;
        task[columnId] = newValue;
        axios.put(`/api/tasks/${rowId}`, task).then(() => {
          console.log('任务更新成功');
        }).catch(e => {
          console.error('任务更新失败', e);
        });
      });
    }
  }
};

createApp({
  components: { TaskGrid },
  template: `<TaskGrid />`
}).mount('#app');
