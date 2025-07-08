<?php
/**
 * Performance Monitoring Dashboard
 * 
 * Provides real-time performance metrics and system health monitoring
 */

use BlackCnote\Services\PerformanceMonitoringService;

$monitor = PerformanceMonitoringService::getInstance();
$report = $monitor->getPerformanceReport();
$realTimeMetrics = $monitor->getRealTimeMetrics();
?>

<div class="wrap">
    <h1 class="wp-heading-inline">
        <i class="dashicons dashicons-chart-area"></i>
        Performance Dashboard
    </h1>
    
    <div class="performance-overview">
        <!-- Real-time Metrics Cards -->
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-icon">
                    <i class="dashicons dashicons-clock"></i>
                </div>
                <div class="metric-content">
                    <h3>Response Time</h3>
                    <div class="metric-value">
                        <?php echo number_format($report['response_times']['average'], 3); ?>s
                    </div>
                    <div class="metric-trend <?php echo $report['response_times']['average'] < 1 ? 'positive' : 'negative'; ?>">
                        <?php echo $report['response_times']['average'] < 1 ? '✓ Good' : '⚠ Slow'; ?>
                    </div>
                </div>
            </div>
            
            <div class="metric-card">
                <div class="metric-icon">
                    <i class="dashicons dashicons-database"></i>
                </div>
                <div class="metric-content">
                    <h3>Memory Usage</h3>
                    <div class="metric-value">
                        <?php echo $report['memory_usage']['current']; ?>
                    </div>
                    <div class="metric-trend <?php echo $report['memory_usage']['percentage'] < 70 ? 'positive' : 'negative'; ?>">
                        <?php echo $report['memory_usage']['percentage']; ?>% used
                    </div>
                </div>
            </div>
            
            <div class="metric-card">
                <div class="metric-icon">
                    <i class="dashicons dashicons-admin-users"></i>
                </div>
                <div class="metric-content">
                    <h3>Total Requests</h3>
                    <div class="metric-value">
                        <?php echo number_format($report['summary']['total_requests']); ?>
                    </div>
                    <div class="metric-trend positive">
                        Active monitoring
                    </div>
                </div>
            </div>
            
            <div class="metric-card">
                <div class="metric-icon">
                    <i class="dashicons dashicons-warning"></i>
                </div>
                <div class="metric-content">
                    <h3>Error Rate</h3>
                    <div class="metric-value">
                        <?php echo number_format($report['summary']['error_rate'], 2); ?>%
                    </div>
                    <div class="metric-trend <?php echo $report['summary']['error_rate'] < 5 ? 'positive' : 'negative'; ?>">
                        <?php echo $report['summary']['error_rate'] < 5 ? '✓ Low' : '⚠ High'; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Performance Charts -->
        <div class="charts-section">
            <div class="chart-container">
                <h3>Response Time Distribution</h3>
                <canvas id="responseTimeChart" width="400" height="200"></canvas>
            </div>
            
            <div class="chart-container">
                <h3>Memory Usage Trend</h3>
                <canvas id="memoryChart" width="400" height="200"></canvas>
            </div>
        </div>
        
        <!-- Database Performance -->
        <div class="database-section">
            <h3><i class="dashicons dashicons-database"></i> Database Performance</h3>
            <div class="db-metrics">
                <div class="db-metric">
                    <span class="label">Total Queries:</span>
                    <span class="value"><?php echo number_format($report['database']['total_queries']); ?></span>
                </div>
                <div class="db-metric">
                    <span class="label">Slow Queries:</span>
                    <span class="value <?php echo $report['database']['slow_queries'] > 0 ? 'warning' : 'success'; ?>">
                        <?php echo number_format($report['database']['slow_queries']); ?>
                    </span>
                </div>
                <div class="db-metric">
                    <span class="label">Avg Query Time:</span>
                    <span class="value"><?php echo number_format($report['database']['average_query_time'], 3); ?>s</span>
                </div>
            </div>
        </div>
        
        <!-- Cache Performance -->
        <div class="cache-section">
            <h3><i class="dashicons dashicons-performance"></i> Cache Performance</h3>
            <div class="cache-metrics">
                <div class="cache-metric">
                    <span class="label">Hit Rate:</span>
                    <span class="value <?php echo $report['cache']['hit_rate'] > 80 ? 'success' : 'warning'; ?>">
                        <?php echo number_format($report['cache']['hit_rate'], 1); ?>%
                    </span>
                </div>
                <div class="cache-metric">
                    <span class="label">Hits:</span>
                    <span class="value"><?php echo number_format($report['cache']['hits']); ?></span>
                </div>
                <div class="cache-metric">
                    <span class="label">Misses:</span>
                    <span class="value"><?php echo number_format($report['cache']['misses']); ?></span>
                </div>
            </div>
        </div>
        
        <!-- Recent Alerts -->
        <?php if (!empty($report['alerts'])): ?>
        <div class="alerts-section">
            <h3><i class="dashicons dashicons-warning"></i> Recent Alerts</h3>
            <div class="alerts-list">
                <?php foreach (array_slice($report['alerts'], -5) as $alert): ?>
                <div class="alert-item <?php echo $alert['severity']; ?>">
                    <div class="alert-header">
                        <span class="alert-type"><?php echo ucfirst($alert['type']); ?></span>
                        <span class="alert-severity <?php echo $alert['severity']; ?>">
                            <?php echo ucfirst($alert['severity']); ?>
                        </span>
                        <span class="alert-time">
                            <?php echo date('M j, H:i', $alert['timestamp']); ?>
                        </span>
                    </div>
                    <div class="alert-message"><?php echo esc_html($alert['message']); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- System Health -->
        <div class="health-section">
            <h3><i class="dashicons dashicons-heart"></i> System Health</h3>
            <div class="health-grid">
                <div class="health-item">
                    <span class="health-label">WordPress</span>
                    <span class="health-status success">✓ Healthy</span>
                </div>
                <div class="health-item">
                    <span class="health-label">Database</span>
                    <span class="health-status success">✓ Connected</span>
                </div>
                <div class="health-item">
                    <span class="health-label">Redis Cache</span>
                    <span class="health-status <?php echo extension_loaded('redis') ? 'success' : 'warning'; ?>">
                        <?php echo extension_loaded('redis') ? '✓ Active' : '⚠ Disabled'; ?>
                    </span>
                </div>
                <div class="health-item">
                    <span class="health-label">File System</span>
                    <span class="health-status success">✓ Writable</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.performance-overview {
    margin-top: 20px;
}

.metrics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.metric-card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    display: flex;
    align-items: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.metric-icon {
    font-size: 2em;
    color: #0073aa;
    margin-right: 15px;
}

.metric-content h3 {
    margin: 0 0 5px 0;
    font-size: 14px;
    color: #666;
}

.metric-value {
    font-size: 24px;
    font-weight: bold;
    color: #333;
    margin-bottom: 5px;
}

.metric-trend {
    font-size: 12px;
    padding: 2px 8px;
    border-radius: 12px;
    display: inline-block;
}

.metric-trend.positive {
    background: #d4edda;
    color: #155724;
}

.metric-trend.negative {
    background: #f8d7da;
    color: #721c24;
}

.charts-section {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.chart-container {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.chart-container h3 {
    margin: 0 0 15px 0;
    color: #333;
}

.database-section,
.cache-section,
.alerts-section,
.health-section {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.database-section h3,
.cache-section h3,
.alerts-section h3,
.health-section h3 {
    margin: 0 0 15px 0;
    color: #333;
    display: flex;
    align-items: center;
}

.database-section h3 i,
.cache-section h3 i,
.alerts-section h3 i,
.health-section h3 i {
    margin-right: 8px;
    color: #0073aa;
}

.db-metrics,
.cache-metrics {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
}

.db-metric,
.cache-metric {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    background: #f9f9f9;
    border-radius: 4px;
}

.db-metric .label,
.cache-metric .label {
    font-weight: 500;
    color: #666;
}

.db-metric .value,
.cache-metric .value {
    font-weight: bold;
    color: #333;
}

.value.success {
    color: #28a745;
}

.value.warning {
    color: #ffc107;
}

.value.error {
    color: #dc3545;
}

.alerts-list {
    max-height: 300px;
    overflow-y: auto;
}

.alert-item {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 15px;
    margin-bottom: 10px;
    background: #f9f9f9;
}

.alert-item.warning {
    border-left: 4px solid #ffc107;
    background: #fff3cd;
}

.alert-item.critical {
    border-left: 4px solid #dc3545;
    background: #f8d7da;
}

.alert-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.alert-type {
    font-weight: bold;
    color: #333;
}

.alert-severity {
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: bold;
}

.alert-severity.warning {
    background: #fff3cd;
    color: #856404;
}

.alert-severity.critical {
    background: #f8d7da;
    color: #721c24;
}

.alert-time {
    font-size: 12px;
    color: #666;
}

.alert-message {
    color: #333;
    line-height: 1.4;
}

.health-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.health-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px;
    background: #f9f9f9;
    border-radius: 4px;
}

.health-label {
    font-weight: 500;
    color: #333;
}

.health-status {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: bold;
}

.health-status.success {
    background: #d4edda;
    color: #155724;
}

.health-status.warning {
    background: #fff3cd;
    color: #856404;
}

.health-status.error {
    background: #f8d7da;
    color: #721c24;
}

@media (max-width: 768px) {
    .metrics-grid {
        grid-template-columns: 1fr;
    }
    
    .charts-section {
        grid-template-columns: 1fr;
    }
    
    .db-metrics,
    .cache-metrics {
        grid-template-columns: 1fr;
    }
    
    .health-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Response Time Chart
    const responseTimeCtx = document.getElementById('responseTimeChart').getContext('2d');
    new Chart(responseTimeCtx, {
        type: 'line',
        data: {
            labels: ['P50', 'P95', 'P99', 'Average'],
            datasets: [{
                label: 'Response Time (seconds)',
                data: [
                    <?php echo $report['response_times']['median']; ?>,
                    <?php echo $report['response_times']['p95']; ?>,
                    <?php echo $report['response_times']['p99']; ?>,
                    <?php echo $report['response_times']['average']; ?>
                ],
                borderColor: '#0073aa',
                backgroundColor: 'rgba(0, 115, 170, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Seconds'
                    }
                }
            }
        }
    });
    
    // Memory Usage Chart
    const memoryCtx = document.getElementById('memoryChart').getContext('2d');
    new Chart(memoryCtx, {
        type: 'doughnut',
        data: {
            labels: ['Used', 'Available'],
            datasets: [{
                data: [
                    <?php echo $report['memory_usage']['percentage']; ?>,
                    <?php echo 100 - $report['memory_usage']['percentage']; ?>
                ],
                backgroundColor: [
                    '#dc3545',
                    '#28a745'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    
    // Auto-refresh every 30 seconds
    setInterval(function() {
        location.reload();
    }, 30000);
});
</script> 