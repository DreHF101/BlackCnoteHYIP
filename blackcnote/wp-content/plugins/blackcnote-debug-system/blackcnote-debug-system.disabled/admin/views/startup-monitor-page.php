<?php
/**
 * BlackCnote Debug System - Startup Monitor Admin Page
 * 
 * @package BlackCnoteDebugSystem
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

$health_report = $health_report ?? [];
$overall_health = $health_report['overall_health'] ?? 'unknown';
$critical_issues = $health_report['metrics']['critical_issues'] ?? 0;
$warnings = $health_report['metrics']['warnings'] ?? 0;
?>

<div class="wrap">
    <h1>
        <span class="dashicons dashicons-admin-tools"></span>
        BlackCnote Startup Monitor
    </h1>
    
    <div class="blackcnote-startup-monitor">
        <!-- Overall Health Status -->
        <div class="health-status-card">
            <h2>Overall System Health</h2>
            <div class="health-indicator health-<?php echo esc_attr($overall_health); ?>">
                <span class="health-icon">
                    <?php if ($overall_health === 'healthy'): ?>
                        <span class="dashicons dashicons-yes-alt"></span>
                    <?php elseif ($overall_health === 'degraded'): ?>
                        <span class="dashicons dashicons-warning"></span>
                    <?php else: ?>
                        <span class="dashicons dashicons-dismiss"></span>
                    <?php endif; ?>
                </span>
                <span class="health-text"><?php echo esc_html(ucfirst($overall_health)); ?></span>
            </div>
            
            <div class="health-metrics">
                <div class="metric">
                    <span class="metric-label">Critical Issues:</span>
                    <span class="metric-value critical"><?php echo esc_html($critical_issues); ?></span>
                </div>
                <div class="metric">
                    <span class="metric-label">Warnings:</span>
                    <span class="metric-value warning"><?php echo esc_html($warnings); ?></span>
                </div>
                <div class="metric">
                    <span class="metric-label">Last Check:</span>
                    <span class="metric-value"><?php echo esc_html($health_report['timestamp'] ?? 'Unknown'); ?></span>
                </div>
            </div>
        </div>
        
        <!-- Startup Script Status -->
        <div class="status-card">
            <h3>Startup Script Status</h3>
            <?php if (isset($health_report['startup_script'])): ?>
                <div class="status-grid">
                    <div class="status-item">
                        <span class="status-label">Script Exists:</span>
                        <span class="status-value <?php echo $health_report['startup_script']['exists'] ? 'success' : 'error'; ?>">
                            <?php echo $health_report['startup_script']['exists'] ? 'Yes' : 'No'; ?>
                        </span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">Readable:</span>
                        <span class="status-value <?php echo $health_report['startup_script']['readable'] ? 'success' : 'error'; ?>">
                            <?php echo $health_report['startup_script']['readable'] ? 'Yes' : 'No'; ?>
                        </span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">Executable:</span>
                        <span class="status-value <?php echo $health_report['startup_script']['executable'] ? 'success' : 'error'; ?>">
                            <?php echo $health_report['startup_script']['executable'] ? 'Yes' : 'No'; ?>
                        </span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">File Size:</span>
                        <span class="status-value"><?php echo esc_html(size_format($health_report['startup_script']['size'] ?? 0)); ?></span>
                    </div>
                </div>
            <?php else: ?>
                <p class="no-data">No startup script data available</p>
            <?php endif; ?>
        </div>
        
        <!-- Docker Services Status -->
        <div class="status-card">
            <h3>Docker Services Status</h3>
            <?php if (isset($health_report['docker_services'])): ?>
                <div class="services-grid">
                    <?php foreach ($health_report['docker_services'] as $service): ?>
                        <div class="service-item">
                            <div class="service-header">
                                <span class="service-name"><?php echo esc_html($service['name']); ?></span>
                                <span class="service-status <?php echo $service['healthy'] ? 'healthy' : 'unhealthy'; ?>">
                                    <?php echo $service['healthy'] ? 'Healthy' : 'Unhealthy'; ?>
                                </span>
                            </div>
                            <div class="service-details">
                                <span class="service-type"><?php echo esc_html(ucfirst($service['type'])); ?></span>
                                <?php if (isset($service['required'])): ?>
                                    <span class="service-required <?php echo $service['required'] ? 'required' : 'optional'; ?>">
                                        <?php echo $service['required'] ? 'Required' : 'Optional'; ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="no-data">No Docker services data available</p>
            <?php endif; ?>
        </div>
        
        <!-- WordPress Services Status -->
        <div class="status-card">
            <h3>WordPress Services Status</h3>
            <?php if (isset($health_report['wordpress_services'])): ?>
                <div class="services-grid">
                    <?php foreach ($health_report['wordpress_services'] as $service): ?>
                        <div class="service-item">
                            <div class="service-header">
                                <span class="service-name"><?php echo esc_html($service['name']); ?></span>
                                <span class="service-status <?php echo $service['healthy'] ? 'healthy' : 'unhealthy'; ?>">
                                    <?php echo $service['healthy'] ? 'Healthy' : 'Unhealthy'; ?>
                                </span>
                            </div>
                            <div class="service-details">
                                <?php if (isset($service['url'])): ?>
                                    <span class="service-url"><?php echo esc_url($service['url']); ?></span>
                                <?php endif; ?>
                                <?php if (isset($service['required'])): ?>
                                    <span class="service-required <?php echo $service['required'] ? 'required' : 'optional'; ?>">
                                        <?php echo $service['required'] ? 'Required' : 'Optional'; ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="no-data">No WordPress services data available</p>
            <?php endif; ?>
        </div>
        
        <!-- System Resources -->
        <div class="status-card">
            <h3>System Resources</h3>
            <?php if (isset($health_report['system_resources'])): ?>
                <div class="resources-grid">
                    <div class="resource-item">
                        <h4>Memory</h4>
                        <div class="resource-details">
                            <div class="resource-row">
                                <span class="resource-label">Limit:</span>
                                <span class="resource-value"><?php echo esc_html($health_report['system_resources']['memory']['limit']); ?></span>
                            </div>
                            <div class="resource-row">
                                <span class="resource-label">Current Usage:</span>
                                <span class="resource-value"><?php echo esc_html(size_format($health_report['system_resources']['memory']['usage'])); ?></span>
                            </div>
                            <div class="resource-row">
                                <span class="resource-label">Peak Usage:</span>
                                <span class="resource-value"><?php echo esc_html(size_format($health_report['system_resources']['memory']['peak'])); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="resource-item">
                        <h4>Disk Space</h4>
                        <div class="resource-details">
                            <div class="resource-row">
                                <span class="resource-label">Free Space:</span>
                                <span class="resource-value"><?php echo esc_html(size_format($health_report['system_resources']['disk']['free'])); ?></span>
                            </div>
                            <div class="resource-row">
                                <span class="resource-label">Total Space:</span>
                                <span class="resource-value"><?php echo esc_html(size_format($health_report['system_resources']['disk']['total'])); ?></span>
                            </div>
                            <div class="resource-row">
                                <span class="resource-label">Usage:</span>
                                <span class="resource-value <?php echo $health_report['system_resources']['disk']['usage_percent'] > 90 ? 'critical' : ''; ?>">
                                    <?php echo esc_html($health_report['system_resources']['disk']['usage_percent']); ?>%
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="resource-item">
                        <h4>PHP Configuration</h4>
                        <div class="resource-details">
                            <div class="resource-row">
                                <span class="resource-label">Version:</span>
                                <span class="resource-value"><?php echo esc_html($health_report['system_resources']['php']['version']); ?></span>
                            </div>
                            <div class="resource-row">
                                <span class="resource-label">Max Execution Time:</span>
                                <span class="resource-value"><?php echo esc_html($health_report['system_resources']['php']['max_execution_time']); ?>s</span>
                            </div>
                            <div class="resource-row">
                                <span class="resource-label">Upload Max Filesize:</span>
                                <span class="resource-value"><?php echo esc_html($health_report['system_resources']['php']['upload_max_filesize']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <p class="no-data">No system resources data available</p>
            <?php endif; ?>
        </div>
        
        <!-- Action Buttons -->
        <div class="action-buttons">
            <button type="button" class="button button-primary" id="refresh-health">
                <span class="dashicons dashicons-update"></span>
                Refresh Health Check
            </button>
            
            <button type="button" class="button button-secondary" id="restart-services">
                <span class="dashicons dashicons-rest-api"></span>
                Restart Services
            </button>
            
            <button type="button" class="button button-secondary" id="export-report">
                <span class="dashicons dashicons-download"></span>
                Export Report
            </button>
        </div>
    </div>
</div>

<style>
.blackcnote-startup-monitor {
    margin-top: 20px;
}

.health-status-card,
.status-card {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

.health-status-card h2,
.status-card h3 {
    margin-top: 0;
    margin-bottom: 15px;
    color: #23282d;
}

.health-indicator {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
    padding: 10px;
    border-radius: 4px;
}

.health-indicator.health-healthy {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.health-indicator.health-degraded {
    background-color: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

.health-indicator.health-critical {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.health-icon {
    margin-right: 10px;
    font-size: 20px;
}

.health-text {
    font-size: 18px;
    font-weight: bold;
}

.health-metrics {
    display: flex;
    gap: 20px;
}

.metric {
    display: flex;
    flex-direction: column;
}

.metric-label {
    font-size: 12px;
    color: #666;
    text-transform: uppercase;
}

.metric-value {
    font-size: 16px;
    font-weight: bold;
}

.metric-value.critical {
    color: #dc3545;
}

.metric-value.warning {
    color: #ffc107;
}

.status-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.status-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 4px;
}

.status-label {
    font-weight: 500;
}

.status-value.success {
    color: #28a745;
}

.status-value.error {
    color: #dc3545;
}

.services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 15px;
}

.service-item {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 15px;
    background: #f8f9fa;
}

.service-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.service-name {
    font-weight: bold;
    color: #23282d;
}

.service-status.healthy {
    color: #28a745;
    font-weight: bold;
}

.service-status.unhealthy {
    color: #dc3545;
    font-weight: bold;
}

.service-details {
    display: flex;
    gap: 10px;
    font-size: 12px;
}

.service-type {
    background: #e9ecef;
    padding: 2px 6px;
    border-radius: 3px;
    text-transform: uppercase;
}

.service-required.required {
    background: #dc3545;
    color: white;
    padding: 2px 6px;
    border-radius: 3px;
}

.service-required.optional {
    background: #6c757d;
    color: white;
    padding: 2px 6px;
    border-radius: 3px;
}

.service-url {
    color: #007cba;
    text-decoration: none;
}

.resources-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.resource-item {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 15px;
    background: #f8f9fa;
}

.resource-item h4 {
    margin-top: 0;
    margin-bottom: 15px;
    color: #23282d;
}

.resource-details {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.resource-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.resource-label {
    font-weight: 500;
    color: #666;
}

.resource-value {
    font-weight: bold;
}

.resource-value.critical {
    color: #dc3545;
}

.action-buttons {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

.action-buttons .button {
    display: flex;
    align-items: center;
    gap: 5px;
}

.no-data {
    color: #666;
    font-style: italic;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Refresh health check
    $('#refresh-health').on('click', function() {
        var button = $(this);
        var originalText = button.text();
        
        button.prop('disabled', true).text('Refreshing...');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'blackcnote_startup_health',
                nonce: '<?php echo wp_create_nonce('blackcnote_debug_nonce'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Failed to refresh health check');
                }
            },
            error: function() {
                alert('Failed to refresh health check');
            },
            complete: function() {
                button.prop('disabled', false).text(originalText);
            }
        });
    });
    
    // Restart services
    $('#restart-services').on('click', function() {
        if (confirm('Are you sure you want to restart all BlackCnote services? This may cause temporary downtime.')) {
            var button = $(this);
            var originalText = button.text();
            
            button.prop('disabled', true).text('Restarting...');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'blackcnote_startup_restart',
                    nonce: '<?php echo wp_create_nonce('blackcnote_debug_nonce'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        alert('Restart request logged. Services will be restarted shortly.');
                    } else {
                        alert('Failed to restart services');
                    }
                },
                error: function() {
                    alert('Failed to restart services');
                },
                complete: function() {
                    button.prop('disabled', false).text(originalText);
                }
            });
        }
    });
    
    // Export report
    $('#export-report').on('click', function() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'blackcnote_startup_health',
                nonce: '<?php echo wp_create_nonce('blackcnote_debug_nonce'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    var dataStr = JSON.stringify(response.data, null, 2);
                    var dataBlob = new Blob([dataStr], {type: 'application/json'});
                    var url = URL.createObjectURL(dataBlob);
                    var link = document.createElement('a');
                    link.href = url;
                    link.download = 'blackcnote-startup-health-' + new Date().toISOString().split('T')[0] + '.json';
                    link.click();
                    URL.revokeObjectURL(url);
                } else {
                    alert('Failed to export report');
                }
            },
            error: function() {
                alert('Failed to export report');
            }
        });
    });
});
</script> 