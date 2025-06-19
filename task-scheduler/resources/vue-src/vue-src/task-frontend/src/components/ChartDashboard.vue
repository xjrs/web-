<template>
    <div class="chart-dashboard">
      <div ref="completionChart" class="chart"></div>
      <div ref="statusChart" class="chart"></div>
      <div ref="priorityChart" class="chart"></div>
    </div>
  </template>
  
  <script>
  import * as echarts from 'echarts'
  
  export default {
    name: 'ChartDashboard',
    props: {
      stats: {
        type: Object,
        required: true
      }
    },
    mounted() {
      this.initCharts()
    },
    watch: {
      stats: {
        handler() {
          this.initCharts()
        },
        deep: true,
        immediate: true
      }
    },
    methods: {
      initCharts() {
        this.initCompletionChart()
        this.initStatusChart()
        this.initPriorityChart()
      },
  
      initCompletionChart() {
        if (this.completionChart) this.completionChart.dispose()
        this.completionChart = echarts.init(this.$refs.completionChart)
        this.completionChart.setOption({
          title: { text: '任务完成率趋势' },
          tooltip: { trigger: 'axis' },
          xAxis: {
            type: 'category',
            data: this.stats.completionRateOverTime?.dates || []
          },
          yAxis: {
            type: 'value',
            max: 100,
            min: 0,
            axisLabel: {
              formatter: '{value} %'
            }
          },
          series: [{
            data: this.stats.completionRateOverTime?.rates || [],
            type: 'line',
            smooth: true,
            areaStyle: {}
          }]
        })
      },
  
      initStatusChart() {
        if (this.statusChart) this.statusChart.dispose()
        this.statusChart = echarts.init(this.$refs.statusChart)
        this.statusChart.setOption({
          title: { text: '任务状态分布' },
          tooltip: { trigger: 'item' },
          legend: { bottom: 10 },
          series: [
            {
              name: '任务状态',
              type: 'pie',
              radius: '50%',
              data: this.stats.taskStatusDistribution || [],
              emphasis: {
                itemStyle: {
                  shadowBlur: 10,
                  shadowOffsetX: 0,
                  shadowColor: 'rgba(0, 0, 0, 0.5)'
                }
              }
            }
          ]
        })
      },
  
      initPriorityChart() {
        if (this.priorityChart) this.priorityChart.dispose()
        this.priorityChart = echarts.init(this.$refs.priorityChart)
        this.priorityChart.setOption({
          title: { text: '任务优先级分布' },
          tooltip: { trigger: 'axis' },
          xAxis: {
            type: 'category',
            data: this.stats.priorityDistribution?.categories || []
          },
          yAxis: { type: 'value' },
          series: [{
            data: this.stats.priorityDistribution?.counts || [],
            type: 'bar',
            barWidth: '40%'
          }]
        })
      }
    },
  
    beforeUnmount() {
      this.completionChart && this.completionChart.dispose()
      this.statusChart && this.statusChart.dispose()
      this.priorityChart && this.priorityChart.dispose()
    }
  }
  </script>
  
  <style scoped>
  .chart-dashboard {
    display: flex;
    justify-content: space-around;
    gap: 20px;
    flex-wrap: wrap;
  }
  .chart {
    width: 30%;
    min-width: 300px;
    height: 300px;
    border: 1px solid #ccc;
    border-radius: 6px;
  }
  </style>
  