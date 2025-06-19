<template>
    <div class="calendar-scheduler">
      <div ref="scheduler" class="dhx_cal_container" style="width: 100%; height: 600px;"></div>
    </div>
  </template>
  
  <script>
  import scheduler from "dhtmlx-scheduler"
  import "dhtmlx-scheduler/codebase/dhtmlxscheduler.css"
  
  export default {
    name: "CalendarScheduler",
    data() {
      return {
        schedulerInitialized: false,
      }
    },
    mounted() {
      // 初始化调度器
      scheduler.init(this.$refs.scheduler, new Date(), "week")
  
      // 加载初始事件数据
      this.loadEvents()
  
      // 事件点击编辑示例
      scheduler.attachEvent("onEventClick", (id, e) => {
        const event = scheduler.getEvent(id)
        alert(`任务名称：${event.text}\n开始时间：${event.start_date}\n结束时间：${event.end_date}`)
        // 这里可以改成弹窗编辑任务等操作
        return true
      })
  
      // 事件添加示例
      scheduler.attachEvent("onEmptyClick", (date, e) => {
        const newId = Date.now()
        scheduler.addEvent({
          id: newId,
          start_date: date,
          end_date: new Date(date.getTime() + 60 * 60 * 1000), // 默认1小时
          text: "新任务"
        })
      })
  
      this.schedulerInitialized = true
    },
    methods: {
      loadEvents() {
        // TODO：从后端接口获取事件数据，例如：
        // fetch('/api/tasks')
        //   .then(res => res.json())
        //   .then(data => {
        //      scheduler.parse(data, 'json');
        //   });
  
        // 目前用模拟数据
        const sampleEvents = [
          {
            id: 1,
            start_date: "2025-06-18 09:00",
            end_date: "2025-06-18 11:00",
            text: "项目会议"
          },
          {
            id: 2,
            start_date: "2025-06-19 13:00",
            end_date: "2025-06-19 15:00",
            text: "开发任务"
          },
          {
            id: 3,
            start_date: "2025-06-20 10:00",
            end_date: "2025-06-20 12:00",
            text: "代码评审"
          }
        ]
  
        scheduler.clearAll()
        scheduler.parse(sampleEvents, "json")
      }
    },
    beforeUnmount() {
      if (this.schedulerInitialized) {
        scheduler.clearAll()
      }
    }
  }
  </script>
  
  <style scoped>
  .calendar-scheduler {
    width: 100%;
    height: 600px;
  }
  .dhx_cal_container {
    border: 1px solid #ccc;
    border-radius: 6px;
  }
  </style>
  