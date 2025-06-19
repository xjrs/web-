<template>
  <div>
    <h2>日程安排</h2>
    <FullCalendar
      :options="calendarOptions"
      ref="fullCalendar"
    />
  </div>
</template>

<script>
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'

export default {
  name: 'CalendarPage',
  components: { FullCalendar },
  data() {
    return {
      calendarOptions: {
        plugins: [dayGridPlugin],
        initialView: 'dayGridMonth',
        events: [],
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridDay,dayGridWeek,dayGridMonth'
        }
      }
    }
  },
  mounted() {
    // 获取任务数据
    fetch("/api/tasks")
      .then(res => res.json())
      .then(data => {
        this.calendarOptions.events = data.map(task => ({
          id: task.id,
          title: task.name,
          start: task.start_time,
          end: task.due_date || task.start_time
        }))
      })
  }
}
</script>