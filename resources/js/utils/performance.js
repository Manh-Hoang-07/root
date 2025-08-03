/**
 * Performance Monitoring Utilities
 */

class PerformanceMonitor {
  constructor() {
    this.metrics = new Map()
    this.observers = []
    this.isEnabled = process.env.NODE_ENV !== 'production'
  }

  // Start timing an operation
  startTimer(name) {
    if (!this.isEnabled) return
    
    const startTime = performance.now()
    this.metrics.set(name, { startTime, endTime: null, duration: null })
    
    return () => this.endTimer(name)
  }

  // End timing an operation
  endTimer(name) {
    if (!this.isEnabled) return
    
    const metric = this.metrics.get(name)
    if (!metric) return
    
    metric.endTime = performance.now()
    metric.duration = metric.endTime - metric.startTime
    
    this.logMetric(name, metric.duration)
    this.notifyObservers(name, metric.duration)
  }

  // Measure component render time
  measureComponentRender(componentName, callback) {
    if (!this.isEnabled) {
      return callback()
    }
    
    const endTimer = this.startTimer(`component-render-${componentName}`)
    
    try {
      const result = callback()
      endTimer()
      return result
    } catch (error) {
      endTimer()
      throw error
    }
  }

  // Measure API call performance
  measureApiCall(endpoint, apiCall) {
    if (!this.isEnabled) {
      return apiCall()
    }
    
    const endTimer = this.startTimer(`api-call-${endpoint}`)
    
    return apiCall()
      .then(result => {
        endTimer()
        return result
      })
      .catch(error => {
        endTimer()
        throw error
      })
  }

  // Measure DOM operation
  measureDomOperation(operationName, operation) {
    if (!this.isEnabled) {
      return operation()
    }
    
    const endTimer = this.startTimer(`dom-operation-${operationName}`)
    
    try {
      const result = operation()
      endTimer()
      return result
    } catch (error) {
      endTimer()
      throw error
    }
  }

  // Get performance metrics
  getMetrics() {
    return Object.fromEntries(this.metrics)
  }

  // Get average duration for a metric
  getAverageDuration(name) {
    const metrics = Array.from(this.metrics.values())
      .filter(metric => metric.duration !== null)
    
    if (metrics.length === 0) return 0
    
    const totalDuration = metrics.reduce((sum, metric) => sum + metric.duration, 0)
    return totalDuration / metrics.length
  }

  // Clear metrics
  clearMetrics() {
    this.metrics.clear()
  }

  // Log metric to console
  logMetric(name, duration) {
    if (!this.isEnabled) return
    
    const color = duration > 1000 ? '🔴' : duration > 500 ? '🟡' : '🟢'
    console.log(`${color} ${name}: ${duration.toFixed(2)}ms`)
  }

  // Add observer for performance events
  addObserver(callback) {
    this.observers.push(callback)
  }

  // Remove observer
  removeObserver(callback) {
    const index = this.observers.indexOf(callback)
    if (index > -1) {
      this.observers.splice(index, 1)
    }
  }

  // Notify observers
  notifyObservers(name, duration) {
    this.observers.forEach(callback => {
      try {
        callback(name, duration)
      } catch (error) {
        console.error('Performance observer error:', error)
      }
    })
  }

  // Enable/disable monitoring
  setEnabled(enabled) {
    this.isEnabled = enabled
  }

  // Get memory usage
  getMemoryUsage() {
    if (!performance.memory) return null
    
    return {
      used: performance.memory.usedJSHeapSize,
      total: performance.memory.totalJSHeapSize,
      limit: performance.memory.jsHeapSizeLimit
    }
  }

  // Get navigation timing
  getNavigationTiming() {
    const navigation = performance.getEntriesByType('navigation')[0]
    if (!navigation) return null
    
    return {
      dns: navigation.domainLookupEnd - navigation.domainLookupStart,
      tcp: navigation.connectEnd - navigation.connectStart,
      ttfb: navigation.responseStart - navigation.requestStart,
      download: navigation.responseEnd - navigation.responseStart,
      domContentLoaded: navigation.domContentLoadedEventEnd - navigation.domContentLoadedEventStart,
      load: navigation.loadEventEnd - navigation.loadEventStart,
      total: navigation.loadEventEnd - navigation.fetchStart
    }
  }

  // Get resource timing
  getResourceTiming() {
    const resources = performance.getEntriesByType('resource')
    return resources.map(resource => ({
      name: resource.name,
      duration: resource.duration,
      size: resource.transferSize,
      type: resource.initiatorType
    }))
  }

  // Monitor long tasks
  monitorLongTasks(callback) {
    if (!this.isEnabled) return
    
    const observer = new PerformanceObserver((list) => {
      list.getEntries().forEach((entry) => {
        if (entry.duration > 50) { // 50ms threshold
          callback(entry)
        }
      })
    })
    
    observer.observe({ entryTypes: ['longtask'] })
    return observer
  }

  // Monitor layout shifts
  monitorLayoutShifts(callback) {
    if (!this.isEnabled) return
    
    const observer = new PerformanceObserver((list) => {
      list.getEntries().forEach((entry) => {
        if (entry.value > 0.1) { // 0.1 threshold
          callback(entry)
        }
      })
    })
    
    observer.observe({ entryTypes: ['layout-shift'] })
    return observer
  }

  // Monitor first input delay
  monitorFirstInput(callback) {
    if (!this.isEnabled) return
    
    const observer = new PerformanceObserver((list) => {
      list.getEntries().forEach((entry) => {
        callback(entry)
      })
    })
    
    observer.observe({ entryTypes: ['first-input'] })
    return observer
  }

  // Generate performance report
  generateReport() {
    const report = {
      metrics: this.getMetrics(),
      memory: this.getMemoryUsage(),
      navigation: this.getNavigationTiming(),
      resources: this.getResourceTiming(),
      timestamp: new Date().toISOString()
    }
    
    return report
  }

  // Send performance data to analytics
  sendToAnalytics(data) {
    if (!this.isEnabled) return
    
    // Send to your analytics service
    console.log('Performance data:', data)
    
    // Example: Send to Google Analytics
    if (window.gtag) {
      window.gtag('event', 'performance', {
        event_category: 'performance',
        event_label: data.name,
        value: Math.round(data.duration)
      })
    }
  }
}

// Create singleton instance
const performanceMonitor = new PerformanceMonitor()

// Vue plugin
export const PerformancePlugin = {
  install(app) {
    app.config.globalProperties.$performance = performanceMonitor
    
    // Monitor component render times
    app.mixin({
      mounted() {
        const componentName = this.$options.name || this.$options.__file || 'Unknown'
        performanceMonitor.measureComponentRender(componentName, () => {
          // Component is already mounted, so we just log the timing
        })
      }
    })
  }
}

// Export utilities
export const measureTime = (name, operation) => {
  return performanceMonitor.measureDomOperation(name, operation)
}

export const measureApiCall = (endpoint, apiCall) => {
  return performanceMonitor.measureApiCall(endpoint, apiCall)
}

export const measureComponentRender = (componentName, callback) => {
  return performanceMonitor.measureComponentRender(componentName, callback)
}

export const getPerformanceMetrics = () => {
  return performanceMonitor.getMetrics()
}

export const getPerformanceReport = () => {
  return performanceMonitor.generateReport()
}

export default performanceMonitor 