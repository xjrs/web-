<template>
  <div class="statistics-page">
    <!-- 页面头部 -->
    <div class="page-header">
      <div class="header-content">
        <div class="title-section">
          <h1 class="page-title">
            <span class="title-icon">📊</span>
            数据统计分析
          </h1>
          <p class="page-subtitle">全面分析项目和任务数据趋势</p>
        </div>
        <div class="header-actions">
          <button class="btn btn-secondary" @click="refreshData">
            <span class="btn-icon">🔄</span>
            刷新数据
          </button>
          <button class="btn btn-primary" @click="exportReport">
            <span class="btn-icon">📄</span>
            导出报告
          </button>
        </div>
      </div>
    </div>

    <!-- 数据概览卡片 -->
    <div class="stats-overview">
      <div class="overview-grid">
        <div class="overview-card">
          <div class="card-icon">📈</div>
          <div class="card-content">
            <div class="card-title">总完成率</div>
            <div class="card-value">{{ overallStats.completionRate }}%</div>
            <div class="card-trend positive">
              <span class="trend-icon">📈</span>
              <span class="trend-text">+{{ overallStats.completionTrend }}%</span>
            </div>
          </div>
        </div>

        <div class="overview-card">
          <div class="card-icon">🎯</div>
          <div class="card-content">
            <div class="card-title">活跃任务</div>
            <div class="card-value">{{ overallStats.activeTasks }}</div>
            <div class="card-trend neutral">
              <span class="trend-icon">➡️</span>
              <span class="trend-text">{{ overallStats.activeTasksTrend }}</span>
            </div>
          </div>
        </div>

        <div class="overview-card">
          <div class="card-icon">⚡</div>
          <div class="card-content">
            <div class="card-title">平均效率</div>
            <div class="card-value">{{ overallStats.avgEfficiency }}%</div>
            <div class="card-trend positive">
              <span class="trend-icon">📈</span>
              <span class="trend-text">+{{ overallStats.efficiencyTrend }}%</span>
            </div>
          </div>
        </div>

        <div class="overview-card">
          <div class="card-icon">⏱️</div>
          <div class="card-content">
            <div class="card-title">延期任务</div>
            <div class="card-value">{{ overallStats.delayedTasks }}</div>
            <div class="card-trend negative">
              <span class="trend-icon">📉</span>
              <span class="trend-text">{{ overallStats.delayedTasksTrend }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 时间范围选择器 -->
    <div class="time-selector">
      <div class="selector-content">
        <label class="selector-label">时间范围：</label>
        <div class="time-buttons">
          <button 
            v-for="period in timePeriods" 
            :key="period.value"
            :class="['time-btn', { active: selectedPeriod === period.value }]"
            @click="selectTimePeriod(period.value)"
          >
            {{ period.label }}
          </button>
        </div>
        <div class="custom-date-range">
          <input 
            type="date" 
            v-model="customDateRange.start"
            @change="updateCustomRange"
            class="date-input"
          />
          <span class="date-separator">至</span>
          <input 
            type="date" 
            v-model="customDateRange.end"
            @change="updateCustomRange"
            class="date-input"
          />
        </div>
      </div>
    </div>

    <!-- 图表容器 -->
    <div class="charts-container">
      <!-- 完成率趋势图 -->
      <div class="chart-card">
        <div class="chart-header">
          <h3 class="chart-title">任务完成率趋势</h3>
          <div class="chart-actions">
            <button class="chart-btn" @click="toggleChartType('completion')">
              <span class="btn-icon">🔄</span>
            </button>
            <button class="chart-btn" @click="fullscreenChart('completion')">
              <span class="btn-icon">🔍</span>
            </button>
          </div>
        </div>
        <div class="chart-content">
          <div ref="completionChart" class="chart" v-show="!loading"></div>
          <div v-if="loading" class="chart-loading">
            <div class="loading-spinner"></div>
            <p>数据加载中...</p>
          </div>
        </div>
      </div>

      <!-- 任务状态分布图 -->
      <div class="chart-card">
        <div class="chart-header">
          <h3 class="chart-title">任务状态分布</h3>
          <div class="chart-actions">
            <button class="chart-btn" @click="toggleChartType('status')">
              <span class="btn-icon">🔄</span>
            </button>
            <button class="chart-btn" @click="fullscreenChart('status')">
              <span class="btn-icon">🔍</span>
            </button>
          </div>
        </div>
        <div class="chart-content">
          <div ref="statusChart" class="chart" v-show="!loading"></div>
          <div v-if="loading" class="chart-loading">
            <div class="loading-spinner"></div>
            <p>数据加载中...</p>
          </div>
        </div>
      </div>

      <!-- 优先级分布图 -->
      <div class="chart-card">
        <div class="chart-header">
          <h3 class="chart-title">任务优先级分布</h3>
          <div class="chart-actions">
            <button class="chart-btn" @click="toggleChartType('priority')">
              <span class="btn-icon">🔄</span>
            </button>
            <button class="chart-btn" @click="fullscreenChart('priority')">
              <span class="btn-icon">🔍</span>
            </button>
          </div>
        </div>
        <div class="chart-content">
          <div ref="priorityChart" class="chart" v-show="!loading"></div>
          <div v-if="loading" class="chart-loading">
            <div class="loading-spinner"></div>
            <p>数据加载中...</p>
          </div>
        </div>
      </div>

      <!-- 工作效率分析图 -->
      <div class="chart-card">
        <div class="chart-header">
          <h3 class="chart-title">工作效率分析</h3>
          <div class="chart-actions">
            <button class="chart-btn" @click="toggleChartType('efficiency')">
              <span class="btn-icon">🔄</span>
            </button>
            <button class="chart-btn" @click="fullscreenChart('efficiency')">
              <span class="btn-icon">🔍</span>
            </button>
          </div>
        </div>
        <div class="chart-content">
          <div ref="efficiencyChart" class="chart" v-show="!loading"></div>
          <div v-if="loading" class="chart-loading">
            <div class="loading-spinner"></div>
            <p>数据加载中...</p>
          </div>
        </div>
      </div>

      <!-- 项目进度对比图 -->
      <div class="chart-card wide">
        <div class="chart-header">
          <h3 class="chart-title">项目进度对比</h3>
          <div class="chart-actions">
            <button class="chart-btn" @click="toggleChartType('progress')">
              <span class="btn-icon">🔄</span>
            </button>
            <button class="chart-btn" @click="fullscreenChart('progress')">
              <span class="btn-icon">🔍</span>
            </button>
          </div>
        </div>
        <div class="chart-content">
          <div ref="progressChart" class="chart" v-show="!loading"></div>
          <div v-if="loading" class="chart-loading">
            <div class="loading-spinner"></div>
            <p>数据加载中...</p>
          </div>
        </div>
      </div>

      <!-- 团队效率热力图 -->
      <div class="chart-card wide">
        <div class="chart-header">
          <h3 class="chart-title">团队效率热力图</h3>
          <div class="chart-actions">
            <button class="chart-btn" @click="toggleChartType('heatmap')">
              <span class="btn-icon">🔄</span>
            </button>
            <button class="chart-btn" @click="fullscreenChart('heatmap')">
              <span class="btn-icon">🔍</span>
            </button>
          </div>
        </div>
        <div class="chart-content">
          <div ref="heatmapChart" class="chart" v-show="!loading"></div>
          <div v-if="loading" class="chart-loading">
            <div class="loading-spinner"></div>
            <p>数据加载中...</p>
          </div>
        </div>
      </div>
    </div>

    <!-- 数据表格 -->
    <div class="data-table-section">
      <div class="table-header">
        <h3 class="table-title">详细数据报表</h3>
        <div class="table-actions">
          <button class="btn btn-secondary" @click="exportTableData">
            <span class="btn-icon">📊</span>
            导出Excel
          </button>
        </div>
      </div>
      <div class="table-container">
        <table class="data-table">
          <thead>
            <tr>
              <th>项目名称</th>
              <th>总任务数</th>
              <th>已完成</th>
              <th>进行中</th>
              <th>待开始</th>
              <th>完成率</th>
              <th>平均用时</th>
              <th>状态</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="project in detailedData" :key="project.id">
              <td class="project-name">{{ project.name }}</td>
              <td>{{ project.totalTasks }}</td>
              <td class="completed">{{ project.completedTasks }}</td>
              <td class="active">{{ project.activeTasks }}</td>
              <td class="pending">{{ project.pendingTasks }}</td>
              <td>
                <div class="progress-cell">
                  <div class="progress-bar-mini">
                    <div 
                      class="progress-fill-mini" 
                      :style="{ width: project.completionRate + '%' }"
                    ></div>
                  </div>
                  <span class="progress-text">{{ project.completionRate }}%</span>
                </div>
              </td>
              <td>{{ project.avgTime }}天</td>
              <td>
                <span 
                  class="status-badge"
                  :class="getProjectStatusClass(project.status)"
                >
                  {{ project.status }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- 全屏图表模态框 -->
    <div v-if="fullscreenModal.show" class="fullscreen-modal" @click="closeFullscreen">
      <div class="fullscreen-content" @click.stop>
        <div class="fullscreen-header">
          <h3>{{ fullscreenModal.title }}</h3>
          <button class="close-btn" @click="closeFullscreen">×</button>
        </div>
        <div class="fullscreen-chart">
          <div :ref="'fullscreen' + fullscreenModal.type" class="chart-fullscreen"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import * as echarts from 'echarts'

export default {
  name: "StatisticsPage",
  data() {
    return {
      loading: false,
      selectedPeriod: '7d',
      customDateRange: {
        start: '',
        end: ''
      },
      timePeriods: [
        { label: '7天', value: '7d' },
        { label: '30天', value: '30d' },
        { label: '90天', value: '90d' },
        { label: '自定义', value: 'custom' }
      ],
      overallStats: {
        completionRate: 78,
        completionTrend: 5,
        activeTasks: 24,
        activeTasksTrend: '持平',
        avgEfficiency: 85,
        efficiencyTrend: 3,
        delayedTasks: 5,
        delayedTasksTrend: '-2'
      },
      taskStats: null,
      detailedData: [
        {
          id: 1,
          name: '企业官网重构',
          totalTasks: 12,
          completedTasks: 8,
          activeTasks: 3,
          pendingTasks: 1,
          completionRate: 67,
          avgTime: 3.5,
          status: '进行中'
        },
        {
          id: 2,
          name: '移动端APP开发',
          totalTasks: 20,
          completedTasks: 15,
          activeTasks: 4,
          pendingTasks: 1,
          completionRate: 75,
          avgTime: 4.2,
          status: '进行中'
        },
        {
          id: 3,
          name: '数据分析平台',
          totalTasks: 15,
          completedTasks: 15,
          activeTasks: 0,
          pendingTasks: 0,
          completionRate: 100,
          avgTime: 2.8,
          status: '已完成'
        }
      ],
      fullscreenModal: {
        show: false,
        type: '',
        title: ''
      },
      // 图表实例
      completionChart: null,
      statusChart: null,
      priorityChart: null,
      efficiencyChart: null,
      progressChart: null,
      heatmapChart: null,
      fullscreenChart: null
    }
  },
  mounted() {
    this.fetchStatistics()
  },
  methods: {
    async fetchStatistics() {
      this.loading = true
      try {
        // 模拟API调用延迟
        await new Promise(resolve => setTimeout(resolve, 1000))
        
        const mockData = {
          completionRateOverTime: {
            dates: ['2025-06-10', '2025-06-11', '2025-06-12', '2025-06-13', '2025-06-14', '2025-06-15', '2025-06-16'],
            rates: [60, 65, 70, 75, 80, 85, 90],
            targets: [70, 72, 74, 76, 78, 80, 82]
          },
          taskStatusDistribution: [
            { name: '已完成', value: 45, itemStyle: { color: '#10b981' } },
            { name: '进行中', value: 30, itemStyle: { color: '#f59e0b' } },
            { name: '待开始', value: 20, itemStyle: { color: '#6b7280' } },
            { name: '已延期', value: 5, itemStyle: { color: '#ef4444' } }
          ],
          priorityDistribution: {
            categories: ['高优先级', '中优先级', '低优先级'],
            counts: [25, 45, 30],
            colors: ['#ef4444', '#f59e0b', '#10b981']
          },
          efficiencyData: {
            dates: ['2025-06-10', '2025-06-11', '2025-06-12', '2025-06-13', '2025-06-14', '2025-06-15', '2025-06-16'],
            efficiency: [75, 80, 85, 82, 88, 90, 85],
            productivity: [70, 75, 80, 85, 87, 85, 88]
          },
          projectProgress: [
            { name: '企业官网重构', planned: 100, actual: 67 },
            { name: '移动端APP开发', planned: 100, actual: 75 },
            { name: '数据分析平台', planned: 100, actual: 100 },
            { name: '客户管理系统', planned: 100, actual: 45 },
            { name: '财务管理模块', planned: 100, actual: 30 }
          ],
          heatmapData: this.generateHeatmapData()
        }
        
        this.taskStats = mockData
        this.initCharts()
      } catch (error) {
        console.error('获取统计数据失败:', error)
      } finally {
        this.loading = false
      }
    },

    generateHeatmapData() {
      const data = []
      const hours = ['00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23']
      const days = ['周一', '周二', '周三', '周四', '周五', '周六', '周日']
      
      for (let i = 0; i < 7; i++) {
        for (let j = 0; j < 24; j++) {
          data.push([j, i, Math.floor(Math.random() * 100)])
        }
      }
      return { hours, days, data }
    },

    initCharts() {
      this.$nextTick(() => {
        this.initCompletionChart()
        this.initStatusChart()
        this.initPriorityChart()
        this.initEfficiencyChart()
        this.initProgressChart()
        this.initHeatmapChart()
      })
    },

    initCompletionChart() {
      if (!this.$refs.completionChart) return
      this.completionChart = echarts.init(this.$refs.completionChart)
      const option = {
        title: {
          text: '',
          left: 'center'
        },
        tooltip: {
          trigger: 'axis',
          axisPointer: {
            type: 'cross'
          }
        },
        legend: {
          data: ['完成率', '目标值'],
          bottom: 0
        },
        grid: {
          left: '3%',
          right: '4%',
          bottom: '10%',
          containLabel: true
        },
        xAxis: {
          type: 'category',
          data: this.taskStats.completionRateOverTime.dates.map(date => 
            new Date(date).toLocaleDateString('zh-CN', { month: 'short', day: 'numeric' })
          ),
          axisLine: { lineStyle: { color: '#e5e7eb' } },
          axisTick: { show: false }
        },
        yAxis: {
          type: 'value',
          max: 100,
          min: 0,
          axisLabel: {
            formatter: '{value}%'
          },
          axisLine: { show: false },
          splitLine: { lineStyle: { color: '#f3f4f6' } }
        },
        series: [
          {
            name: '完成率',
            data: this.taskStats.completionRateOverTime.rates,
            type: 'line',
            smooth: true,
            lineStyle: { color: '#10b981', width: 3 },
            areaStyle: {
              color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                { offset: 0, color: 'rgba(16, 185, 129, 0.3)' },
                { offset: 1, color: 'rgba(16, 185, 129, 0.05)' }
              ])
            },
            symbol: 'circle',
            symbolSize: 6
          },
          {
            name: '目标值',
            data: this.taskStats.completionRateOverTime.targets,
            type: 'line',
            lineStyle: { color: '#f59e0b', width: 2, type: 'dashed' },
            symbol: 'none'
          }
        ]
      }
      this.completionChart.setOption(option)
    },

    initStatusChart() {
      if (!this.$refs.statusChart) return
      this.statusChart = echarts.init(this.$refs.statusChart)
      const option = {
        tooltip: {
          trigger: 'item',
          formatter: '{a} <br/>{b}: {c} ({d}%)'
        },
        legend: {
          bottom: 10,
          left: 'center'
        },
        series: [
          {
            name: '任务状态',
            type: 'pie',
            radius: ['40%', '70%'],
            avoidLabelOverlap: false,
            label: {
              show: false,
              position: 'center'
            },
            emphasis: {
              label: {
                show: true,
                fontSize: '18',
                fontWeight: 'bold'
              }
            },
            labelLine: {
              show: false
            },
            data: this.taskStats.taskStatusDistribution
          }
        ]
      }
      this.statusChart.setOption(option)
    },

    initPriorityChart() {
      if (!this.$refs.priorityChart) return
      this.priorityChart = echarts.init(this.$refs.priorityChart)
      const option = {
        tooltip: {
          trigger: 'axis',
          axisPointer: {
            type: 'shadow'
          }
        },
        grid: {
          left: '3%',
          right: '4%',
          bottom: '3%',
          containLabel: true
        },
        xAxis: {
          type: 'category',
          data: this.taskStats.priorityDistribution.categories,
          axisLine: { lineStyle: { color: '#e5e7eb' } },
          axisTick: { show: false }
        },
        yAxis: {
          type: 'value',
          axisLine: { show: false },
          splitLine: { lineStyle: { color: '#f3f4f6' } }
        },
        series: [{
          data: this.taskStats.priorityDistribution.counts.map((value, index) => ({
            value,
            itemStyle: {
              color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                { offset: 0, color: this.taskStats.priorityDistribution.colors[index] },
                { offset: 1, color: this.taskStats.priorityDistribution.colors[index] + '80' }
              ])
            }
          })),
          type: 'bar',
          barWidth: '50%',
          borderRadius: [4, 4, 0, 0]
        }]
      }
      this.priorityChart.setOption(option)
    },

    initEfficiencyChart() {
      if (!this.$refs.efficiencyChart) return
      this.efficiencyChart = echarts.init(this.$refs.efficiencyChart)
      const option = {
        tooltip: {
          trigger: 'axis'
        },
        legend: {
          data: ['工作效率', '生产力'],
          bottom: 0
        },
        grid: {
          left: '3%',
          right: '4%',
          bottom: '10%',
          containLabel: true
        },
        xAxis: {
          type: 'category',
          data: this.taskStats.efficiencyData.dates.map(date => 
            new Date(date).toLocaleDateString('zh-CN', { month: 'short', day: 'numeric' })
          ),
          axisLine: { lineStyle: { color: '#e5e7eb' } },
          axisTick: { show: false }
        },
        yAxis: {
          type: 'value',
          axisLabel: {
            formatter: '{value}%'
          },
          axisLine: { show: false },
          splitLine: { lineStyle: { color: '#f3f4f6' } }
        },
        series: [
          {
            name: '工作效率',
            type: 'line',
            data: this.taskStats.efficiencyData.efficiency,
            smooth: true,
            lineStyle: { color: '#6366f1', width: 3 },
            symbol: 'circle',
            symbolSize: 6
          },
          {
            name: '生产力',
            type: 'line',
            data: this.taskStats.efficiencyData.productivity,
            smooth: true,
            lineStyle: { color: '#8b5cf6', width: 3 },
            symbol: 'circle',
            symbolSize: 6
          }
        ]
      }
      this.efficiencyChart.setOption(option)
    },

    initProgressChart() {
      if (!this.$refs.progressChart) return
      this.progressChart = echarts.init(this.$refs.progressChart)
      const option = {
        tooltip: {
          trigger: 'axis',
          axisPointer: {
            type: 'shadow'
          }
        },
        legend: {
          data: ['计划进度', '实际进度'],
          bottom: 0
        },
        grid: {
          left: '3%',
          right: '4%',
          bottom: '10%',
          containLabel: true
        },
        xAxis: {
          type: 'category',
          data: this.taskStats.projectProgress.map(item => item.name),
          axisLine: { lineStyle: { color: '#e5e7eb' } },
          axisTick: { show: false }
        },
        yAxis: {
          type: 'value',
          max: 100,
          axisLabel: {
            formatter: '{value}%'
          },
          axisLine: { show: false },
          splitLine: { lineStyle: { color: '#f3f4f6' } }
        },
        series: [
          {
            name: '计划进度',
            type: 'bar',
            data: this.taskStats.projectProgress.map(item => item.planned),
            itemStyle: { color: '#e5e7eb' },
            barWidth: '60%'
          },
          {
            name: '实际进度',
            type: 'bar',
            data: this.taskStats.projectProgress.map(item => item.actual),
            itemStyle: { color: '#10b981' },
            barWidth: '60%'
          }
        ]
      }
      this.progressChart.setOption(option)
    },

    initHeatmapChart() {
      if (!this.$refs.heatmapChart) return
      this.heatmapChart = echarts.init(this.$refs.heatmapChart)
      const option = {
        tooltip: {
          position: 'top',
          formatter: function (params) {
            return `${params.value[1]}时 ${params.name}: ${params.value[2]}%`
          }
        },
        grid: {
          height: '50%',
          top: '10%'
        },
        xAxis: {
          type: 'category',
          data: this.taskStats.heatmapData.hours,
          splitArea: {
            show: true
          }
        },
        yAxis: {
          type: 'category',
          data: this.taskStats.heatmapData.days,
          splitArea: {
            show: true
          }
        },
        visualMap: {
          min: 0,
          max: 100,
          calculable: true,
          orient: 'horizontal',
          left: 'center',
          bottom: '15%',
          inRange: {
            color: ['#f3f4f6', '#10b981']
          }
        },
        series: [{
          name: '工作效率',
          type: 'heatmap',
          data: this.taskStats.heatmapData.data,
          label: {
            show: false
          },
          emphasis: {
            itemStyle: {
              shadowBlur: 10,
              shadowColor: 'rgba(0, 0, 0, 0.5)'
            }
          }
        }]
      }
      this.heatmapChart.setOption(option)
    },

    selectTimePeriod(period) {
      this.selectedPeriod = period
      if (period !== 'custom') {
        this.fetchStatistics()
      }
    },

    updateCustomRange() {
      if (this.customDateRange.start && this.customDateRange.end) {
        this.selectedPeriod = 'custom'
        this.fetchStatistics()
      }
    },

    refreshData() {
      this.fetchStatistics()
    },

    exportReport() {
      // 导出报告逻辑
      console.log('导出报告')
    },

    exportTableData() {
      // 导出表格数据逻辑
      console.log('导出表格数据')
    },

    toggleChartType(chartType) {
      // 切换图表类型逻辑
      console.log('切换图表类型:', chartType)
    },

    fullscreenChart(chartType) {
      const titles = {
        completion: '任务完成率趋势',
        status: '任务状态分布',
        priority: '任务优先级分布',
        efficiency: '工作效率分析',
        progress: '项目进度对比',
        heatmap: '团队效率热力图'
      }
      
      this.fullscreenModal = {
        show: true,
        type: chartType,
        title: titles[chartType]
      }
      
      this.$nextTick(() => {
        this.initFullscreenChart(chartType)
      })
    },

    initFullscreenChart(chartType) {
      const refName = 'fullscreen' + chartType
      if (!this.$refs[refName]) return
      
      if (this.fullscreenChart) {
        this.fullscreenChart.dispose()
      }
      
      this.fullscreenChart = echarts.init(this.$refs[refName])
      
      // 根据图表类型获取对应的配置
      let option = {}
      switch(chartType) {
        case 'completion':
          option = this.getCompletionChartOption(true)
          break
        case 'status':
          option = this.getStatusChartOption(true)
          break
        case 'priority':
          option = this.getPriorityChartOption(true)
          break
        case 'efficiency':
          option = this.getEfficiencyChartOption(true)
          break
        case 'progress':
          option = this.getProgressChartOption(true)
          break
        case 'heatmap':
          option = this.getHeatmapChartOption(true)
          break
      }
      
      this.fullscreenChart.setOption(option)
    },

    getCompletionChartOption(isFullscreen = false) {
      return {
        title: {
          text: isFullscreen ? '任务完成率趋势' : '',
          left: 'center',
          textStyle: { fontSize: isFullscreen ? 20 : 14 }
        },
        tooltip: {
          trigger: 'axis',
          axisPointer: { type: 'cross' }
        },
        legend: {
          data: ['完成率', '目标值'],
          bottom: isFullscreen ? 20 : 0
        },
        grid: {
          left: '3%',
          right: '4%',
          bottom: isFullscreen ? '15%' : '10%',
          top: isFullscreen ? '15%' : '10%',
          containLabel: true
        },
        xAxis: {
          type: 'category',
          data: this.taskStats.completionRateOverTime.dates.map(date => 
            new Date(date).toLocaleDateString('zh-CN', { month: 'short', day: 'numeric' })
          ),
          axisLine: { lineStyle: { color: '#e5e7eb' } },
          axisTick: { show: false }
        },
        yAxis: {
          type: 'value',
          max: 100,
          min: 0,
          axisLabel: { formatter: '{value}%' },
          axisLine: { show: false },
          splitLine: { lineStyle: { color: '#f3f4f6' } }
        },
        series: [
          {
            name: '完成率',
            data: this.taskStats.completionRateOverTime.rates,
            type: 'line',
            smooth: true,
            lineStyle: { color: '#10b981', width: 3 },
            areaStyle: {
              color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                { offset: 0, color: 'rgba(16, 185, 129, 0.3)' },
                { offset: 1, color: 'rgba(16, 185, 129, 0.05)' }
              ])
            },
            symbol: 'circle',
            symbolSize: 6
          },
          {
            name: '目标值',
            data: this.taskStats.completionRateOverTime.targets,
            type: 'line',
            lineStyle: { color: '#f59e0b', width: 2, type: 'dashed' },
            symbol: 'none'
          }
        ]
      }
    },

    getStatusChartOption(isFullscreen = false) {
      return {
        title: {
          text: isFullscreen ? '任务状态分布' : '',
          left: 'center',
          textStyle: { fontSize: isFullscreen ? 20 : 14 }
        },
        tooltip: {
          trigger: 'item',
          formatter: '{a} <br/>{b}: {c} ({d}%)'
        },
        legend: {
          bottom: isFullscreen ? 20 : 10,
          left: 'center'
        },
        series: [
          {
            name: '任务状态',
            type: 'pie',
            radius: isFullscreen ? ['30%', '60%'] : ['40%', '70%'],
            center: ['50%', isFullscreen ? '45%' : '50%'],
            avoidLabelOverlap: false,
            label: { show: false, position: 'center' },
            emphasis: {
              label: {
                show: true,
                fontSize: isFullscreen ? '20' : '18',
                fontWeight: 'bold'
              }
            },
            labelLine: { show: false },
            data: this.taskStats.taskStatusDistribution
          }
        ]
      }
    },

    getPriorityChartOption(isFullscreen = false) {
      return {
        title: {
          text: isFullscreen ? '任务优先级分布' : '',
          left: 'center',
          textStyle: { fontSize: isFullscreen ? 20 : 14 }
        },
        tooltip: {
          trigger: 'axis',
          axisPointer: { type: 'shadow' }
        },
        grid: {
          left: '3%',
          right: '4%',
          bottom: isFullscreen ? '10%' : '3%',
          top: isFullscreen ? '15%' : '10%',
          containLabel: true
        },
        xAxis: {
          type: 'category',
          data: this.taskStats.priorityDistribution.categories,
          axisLine: { lineStyle: { color: '#e5e7eb' } },
          axisTick: { show: false }
        },
        yAxis: {
          type: 'value',
          axisLine: { show: false },
          splitLine: { lineStyle: { color: '#f3f4f6' } }
        },
        series: [{
          data: this.taskStats.priorityDistribution.counts.map((value, index) => ({
            value,
            itemStyle: {
              color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                { offset: 0, color: this.taskStats.priorityDistribution.colors[index] },
                { offset: 1, color: this.taskStats.priorityDistribution.colors[index] + '80' }
              ])
            }
          })),
          type: 'bar',
          barWidth: '50%',
          borderRadius: [4, 4, 0, 0]
        }]
      }
    },

    getEfficiencyChartOption(isFullscreen = false) {
      return {
        title: {
          text: isFullscreen ? '工作效率分析' : '',
          left: 'center',
          textStyle: { fontSize: isFullscreen ? 20 : 14 }
        },
        tooltip: { trigger: 'axis' },
        legend: {
          data: ['工作效率', '生产力'],
          bottom: isFullscreen ? 20 : 0
        },
        grid: {
          left: '3%',
          right: '4%',
          bottom: isFullscreen ? '15%' : '10%',
          top: isFullscreen ? '15%' : '10%',
          containLabel: true
        },
        xAxis: {
          type: 'category',
          data: this.taskStats.efficiencyData.dates.map(date => 
            new Date(date).toLocaleDateString('zh-CN', { month: 'short', day: 'numeric' })
          ),
          axisLine: { lineStyle: { color: '#e5e7eb' } },
          axisTick: { show: false }
        },
        yAxis: {
          type: 'value',
          axisLabel: { formatter: '{value}%' },
          axisLine: { show: false },
          splitLine: { lineStyle: { color: '#f3f4f6' } }
        },
        series: [
          {
            name: '工作效率',
            type: 'line',
            data: this.taskStats.efficiencyData.efficiency,
            smooth: true,
            lineStyle: { color: '#6366f1', width: 3 },
            symbol: 'circle',
            symbolSize: 6
          },
          {
            name: '生产力',
            type: 'line',
            data: this.taskStats.efficiencyData.productivity,
            smooth: true,
            lineStyle: { color: '#8b5cf6', width: 3 },
            symbol: 'circle',
            symbolSize: 6
          }
        ]
      }
    },

    getProgressChartOption(isFullscreen = false) {
      return {
        title: {
          text: isFullscreen ? '项目进度对比' : '',
          left: 'center',
          textStyle: { fontSize: isFullscreen ? 20 : 14 }
        },
        tooltip: {
          trigger: 'axis',
          axisPointer: { type: 'shadow' }
        },
        legend: {
          data: ['计划进度', '实际进度'],
          bottom: isFullscreen ? 20 : 0
        },
        grid: {
          left: '3%',
          right: '4%',
          bottom: isFullscreen ? '15%' : '10%',
          top: isFullscreen ? '15%' : '10%',
          containLabel: true
        },
        xAxis: {
          type: 'category',
          data: this.taskStats.projectProgress.map(item => item.name),
          axisLine: { lineStyle: { color: '#e5e7eb' } },
          axisTick: { show: false },
          axisLabel: {
            interval: 0,
            rotate: isFullscreen ? 0 : 30
          }
        },
        yAxis: {
          type: 'value',
          max: 100,
          axisLabel: { formatter: '{value}%' },
          axisLine: { show: false },
          splitLine: { lineStyle: { color: '#f3f4f6' } }
        },
        series: [
          {
            name: '计划进度',
            type: 'bar',
            data: this.taskStats.projectProgress.map(item => item.planned),
            itemStyle: { color: '#e5e7eb' },
            barWidth: '60%'
          },
          {
            name: '实际进度',
            type: 'bar',
            data: this.taskStats.projectProgress.map(item => item.actual),
            itemStyle: { color: '#10b981' },
            barWidth: '60%'
          }
        ]
      }
    },

    getHeatmapChartOption(isFullscreen = false) {
      return {
        title: {
          text: isFullscreen ? '团队效率热力图' : '',
          left: 'center',
          textStyle: { fontSize: isFullscreen ? 20 : 14 }
        },
        tooltip: {
          position: 'top',
          formatter: function (params) {
            const hours = ['00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23']
            const days = ['周一', '周二', '周三', '周四', '周五', '周六', '周日']
            return `${days[params.value[1]]} ${hours[params.value[0]]}时<br/>效率: ${params.value[2]}%`
          }
        },
        grid: {
          height: isFullscreen ? '60%' : '50%',
          top: isFullscreen ? '20%' : '10%'
        },
        xAxis: {
          type: 'category',
          data: this.taskStats.heatmapData.hours,
          splitArea: { show: true },
          axisLabel: { fontSize: isFullscreen ? 12 : 10 }
        },
        yAxis: {
          type: 'category',
          data: this.taskStats.heatmapData.days,
          splitArea: { show: true },
          axisLabel: { fontSize: isFullscreen ? 12 : 10 }
        },
        visualMap: {
          min: 0,
          max: 100,
          calculable: true,
          orient: 'horizontal',
          left: 'center',
          bottom: isFullscreen ? '5%' : '15%',
          inRange: { color: ['#f3f4f6', '#10b981'] }
        },
        series: [{
          name: '工作效率',
          type: 'heatmap',
          data: this.taskStats.heatmapData.data,
          label: { show: false },
          emphasis: {
            itemStyle: {
              shadowBlur: 10,
              shadowColor: 'rgba(0, 0, 0, 0.5)'
            }
          }
        }]
      }
    },

    closeFullscreen() {
      this.fullscreenModal.show = false
      if (this.fullscreenChart) {
        this.fullscreenChart.dispose()
        this.fullscreenChart = null
      }
    },

    getProjectStatusClass(status) {
      const statusMap = {
        '已完成': 'completed',
        '进行中': 'active',
        '待开始': 'pending',
        '已暂停': 'paused',
        '已取消': 'cancelled'
      }
      return statusMap[status] || 'pending'
    }
  },

  beforeDestroy() {
    // 销毁所有图表实例
    if (this.completionChart) this.completionChart.dispose()
    if (this.statusChart) this.statusChart.dispose()
    if (this.priorityChart) this.priorityChart.dispose()
    if (this.efficiencyChart) this.efficiencyChart.dispose()
    if (this.progressChart) this.progressChart.dispose()
    if (this.heatmapChart) this.heatmapChart.dispose()
    if (this.fullscreenChart) this.fullscreenChart.dispose()
  }
}
</script>

<style scoped>
.statistics-page {
  padding: 24px;
  background-color: #f8fafc;
  min-height: 100vh;
}

/* 页面头部样式 */
.page-header {
  margin-bottom: 24px;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: white;
  padding: 20px 24px;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.title-section h1 {
  margin: 0;
  font-size: 24px;
  font-weight: 600;
  color: #1f2937;
  display: flex;
  align-items: center;
  gap: 8px;
}

.page-subtitle {
  margin: 4px 0 0 0;
  color: #6b7280;
  font-size: 14px;
}

.header-actions {
  display: flex;
  gap: 12px;
}

.btn {
  padding: 8px 16px;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: all 0.2s;
}

.btn-primary {
  background: #3b82f6;
  color: white;
}

.btn-primary:hover {
  background: #2563eb;
}

.btn-secondary {
  background: #f3f4f6;
  color: #374151;
}

.btn-secondary:hover {
  background: #e5e7eb;
}

/* 数据概览卡片样式 */
.stats-overview {
  margin-bottom: 24px;
}

.overview-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 16px;
}

.overview-card {
  background: white;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  gap: 16px;
}

.card-icon {
  font-size: 32px;
  width: 64px;
  height: 64px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f8fafc;
  border-radius: 12px;
}

.card-content {
  flex: 1;
}

.card-title {
  font-size: 14px;
  color: #6b7280;
  margin-bottom: 4px;
}

.card-value {
  font-size: 24px;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 4px;
}

.card-trend {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 12px;
}

.card-trend.positive {
  color: #10b981;
}

.card-trend.negative {
  color: #ef4444;
}

.card-trend.neutral {
  color: #6b7280;
}

/* 时间选择器样式 */
.time-selector {
  margin-bottom: 24px;
}

.selector-content {
  background: white;
  padding: 20px 24px;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
}

.selector-label {
  font-weight: 500;
  color: #374151;
}

.time-buttons {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.time-btn {
  padding: 8px 16px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  background: white;
  color: #374151;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.2s;
}

.time-btn:hover {
  border-color: #3b82f6;
  color: #3b82f6;
}

.time-btn.active {
  background: #3b82f6;
  border-color: #3b82f6;
  color: white;
}

.custom-date-range {
  display: flex;
  align-items: center;
  gap: 8px;
}

.date-input {
  padding: 8px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 14px;
}

.date-separator {
  color: #6b7280;
  font-size: 14px;
}

/* 图表容器样式 */
.charts-container {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 24px;
  margin-bottom: 24px;
}

.chart-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 3php rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.chart-card.wide {
  grid-column: span 2;
}

.chart-header {
  padding: 20px 24px 0;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.chart-title {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
  color: #1f2937;
}

.chart-actions {
  display: flex;
  gap: 8px;
}

.chart-btn {
  padding: 6px 10px;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  background: white;
  color: #6b7280;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.2s;
}

.chart-btn:hover {
  border-color: #3b82f6;
  color: #3b82f6;
}

.chart-content {
  padding: 20px 24px;
  position: relative;
}

.chart {
  width: 100%;
  height: 300px;
}

.chart-loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 300px;
  color: #6b7280;
}

.loading-spinner {
  width: 32px;
  height: 32px;
  border: 3px solid #f3f4f6;
  border-top: 3px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 12px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* 数据表格样式 */
.data-table-section {
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.table-header {
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.table-title {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
  color: #1f2937;
}

.table-container {
  overflow-x: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table th,
.data-table td {
  padding: 12px 16px;
  text-align: left;
  border-bottom: 1px solid #f3f4f6;
}

.data-table th {
  background: #f8fafc;
  font-weight: 600;
  color: #374151;
  font-size: 14px;
}

.data-table td {
  color: #1f2937;
  font-size: 14px;
}

.project-name {
  font-weight: 500;
}

.completed {
  color: #10b981;
  font-weight: 500;
}

.active {
  color: #f59e0b;
  font-weight: 500;
}

.pending {
  color: #6b7280;
  font-weight: 500;
}

.progress-cell {
  display: flex;
  align-items: center;
  gap: 12px;
}

.progress-bar-mini {
  width: 60px;
  height: 6px;
  background: #f3f4f6;
  border-radius: 3px;
  overflow: hidden;
}

.progress-fill-mini {
  height: 100%;
  background: #10b981;
  transition: width 0.3s ease;
}

.progress-text {
  font-size: 12px;
  color: #6b7280;
  min-width: 35px;
}

.status-badge {
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
}

.status-badge.completed {
  background: #d1fae5;
  color: #065f46;
}

.status-badge.active {
  background: #fef3c7;
  color: #92400e;
}

.status-badge.pending {
  background: #f3f4f6;
  color: #374151;
}

.status-badge.paused {
  background: #e0e7ff;
  color: #3730a3;
}

.status-badge.cancelled {
  background: #fee2e2;
  color: #991b1b;
}

/* 全屏模态框样式 */
.fullscreen-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.8);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.fullscreen-content {
  background: white;
  border-radius: 12px;
  width: 90vw;
  height: 80vh;
  max-width: 1200px;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.fullscreen-header {
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.fullscreen-header h3 {
  margin: 0;
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
}

.close-btn {
  width: 32px;
  height: 32px;
  border: none;
  background: #f3f4f6;
  border-radius: 6px;
  color: #6b7280;
  font-size: 18px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.close-btn:hover {
  background: #e5e7eb;
  color: #374151;
}

.fullscreen-chart {
  flex: 1;
  padding: 20px;
}

.chart-fullscreen {
  width: 100%;
  height: 100%;
}

/* 响应式设计 */
@media (max-width: 768px) {
  .statistics-page {
    padding: 16px;
  }
  
  .charts-container {
    grid-template-columns: 1fr;
  }
  
  .chart-card.wide {
    grid-column: span 1;
  }
  
  .header-content {
    flex-direction: column;
    gap: 16px;
    align-items: stretch;
  }
  
  .selector-content {
    flex-direction: column;
    align-items: stretch;
    gap: 12px;
  }
  
  .overview-grid {
    grid-template-columns: 1fr;
  }
  
  .fullscreen-content {
    width: 95vw;
    height: 90vh;
  }
}

@media (max-width: 480px) {
  .overview-card {
    flex-direction: column;
    text-align: center;
  }
  
  .card-icon {
    margin-bottom: 8px;
  }
  
  .table-container {
    font-size: 12px;
  }
  
  .data-table th,
  .data-table td {
    padding: 8px 12px;
  }
}
</style>