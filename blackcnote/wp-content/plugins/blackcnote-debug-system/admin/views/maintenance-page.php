<?php
/**
 * BlackCnote Maintenance Automation Admin Page
 *
 * @package BlackCnoteDebugSystem
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get maintenance status
$maintenance_status = get_option('blackcnote_maintenance_status', []);
$last_daily = get_option('blackcnote_last_daily_maintenance');
$last_weekly = get_option('blackcnote_last_weekly_maintenance');
$last_monthly = get_option('blackcnote_last_monthly_maintenance');
?>

<div class="wrap">
    <h1>🛠️ BlackCnote Maintenance Automation</h1>
    
    <div class="notice notice-info">
        <p><strong>Automated Maintenance System</strong> - This system automatically maintains your BlackCnote installation through scheduled tasks and manual operations.</p>
    </div>
    
    <!-- Maintenance Status Dashboard -->
    <div class="card">
        <h2>📊 Maintenance Status Dashboard</h2>
        <div class="maintenance-status-grid">
            <div class="status-item">
                <h3>Daily Maintenance</h3>
                <p><strong>Last Run:</strong> <?php echo $last_daily ? date('Y-m-d H:i:s', $last_daily) : 'Never'; ?></p>
                <p><strong>Next Run:</strong> <?php echo wp_next_scheduled('blackcnote_daily_maintenance') ? date('Y-m-d H:i:s', wp_next_scheduled('blackcnote_daily_maintenance')) : 'Not scheduled'; ?></p>
                <button class="button button-primary run-maintenance" data-type="daily">Run Daily Maintenance</button>
            </div>
            
            <div class="status-item">
                <h3>Weekly Maintenance</h3>
                <p><strong>Last Run:</strong> <?php echo $last_weekly ? date('Y-m-d H:i:s', $last_weekly) : 'Never'; ?></p>
                <p><strong>Next Run:</strong> <?php echo wp_next_scheduled('blackcnote_weekly_maintenance') ? date('Y-m-d H:i:s', wp_next_scheduled('blackcnote_weekly_maintenance')) : 'Not scheduled'; ?></p>
                <button class="button button-primary run-maintenance" data-type="weekly">Run Weekly Maintenance</button>
            </div>
            
            <div class="status-item">
                <h3>Monthly Maintenance</h3>
                <p><strong>Last Run:</strong> <?php echo $last_monthly ? date('Y-m-d H:i:s', $last_monthly) : 'Never'; ?></p>
                <p><strong>Next Run:</strong> <?php echo wp_next_scheduled('blackcnote_monthly_maintenance') ? date('Y-m-d H:i:s', wp_next_scheduled('blackcnote_monthly_maintenance')) : 'Not scheduled'; ?></p>
                <button class="button button-primary run-maintenance" data-type="monthly">Run Monthly Maintenance</button>
            </div>
        </div>
    </div>
    
    <!-- Manual Maintenance Operations -->
    <div class="card">
        <h2>🔧 Manual Maintenance Operations</h2>
        <div class="manual-operations-grid">
            <div class="operation-item">
                <h3>🧹 Cleanup Temporary Files</h3>
                <p>Remove temporary files, cache files, and other unnecessary files from the system.</p>
                <button class="button run-operation" data-operation="cleanup_temp_files">Run Cleanup</button>
            </div>
            
            <div class="operation-item">
                <h3>🛣️ Verify Canonical Paths</h3>
                <p>Verify that all canonical pathways are intact and accessible.</p>
                <button class="button run-operation" data-operation="verify_canonical_paths">Verify Paths</button>
            </div>
            
            <div class="operation-item">
                <h3>💾 Backup Essential Files</h3>
                <p>Create backups of all essential configuration and core files.</p>
                <button class="button run-operation" data-operation="backup_essential_files">Create Backup</button>
            </div>
            
            <div class="operation-item">
                <h3>📝 Update Documentation</h3>
                <p>Update canonical pathways and other essential documentation.</p>
                <button class="button run-operation" data-operation="update_documentation">Update Docs</button>
            </div>
            
            <div class="operation-item">
                <h3>⚡ Optimize Scripts</h3>
                <p>Analyze and optimize automation scripts for better performance.</p>
                <button class="button run-operation" data-operation="optimize_scripts">Optimize Scripts</button>
            </div>
            
            <div class="operation-item">
                <h3>🔍 System Health Check</h3>
                <p>Perform comprehensive system health and performance analysis.</p>
                <button class="button run-operation" data-operation="check_system_health">Health Check</button>
            </div>
        </div>
    </div>
    
    <!-- Maintenance Log -->
    <div class="card">
        <h2>📋 Maintenance Log</h2>
        <div id="maintenance-log">
            <p>Loading maintenance log...</p>
        </div>
        <button class="button refresh-log">Refresh Log</button>
    </div>
    
    <!-- Results Display -->
    <div class="card" id="maintenance-results" style="display: none;">
        <h2>📊 Maintenance Results</h2>
        <div id="results-content"></div>
    </div>
</div>

<style>
.maintenance-status-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.status-item {
    background: #f9f9f9;
    padding: 20px;
    border-radius: 8px;
    border-left: 4px solid #0073aa;
}

.manual-operations-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
    margin: 20px 0;
}

.operation-item {
    background: #f9f9f9;
    padding: 15px;
    border-radius: 6px;
    border-left: 4px solid #46b450;
}

.operation-item h3 {
    margin-top: 0;
    color: #23282d;
}

.operation-item p {
    color: #666;
    margin-bottom: 15px;
}

.card {
    background: white;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
    margin: 20px 0;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

#maintenance-log {
    background: #f1f1f1;
    padding: 15px;
    border-radius: 4px;
    font-family: monospace;
    max-height: 300px;
    overflow-y: auto;
    margin: 15px 0;
}

.loading {
    opacity: 0.6;
    pointer-events: none;
}

.success {
    border-left-color: #46b450 !important;
}

.error {
    border-left-color: #dc3232 !important;
}

.warning {
    border-left-color: #ffb900 !important;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Run maintenance
    $('.run-maintenance').on('click', function() {
        var type = $(this).data('type');
        var button = $(this);
        
        button.prop('disabled', true).text('Running...');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'blackcnote_run_maintenance',
                type: type,
                nonce: '<?php echo wp_create_nonce('blackcnote_maintenance_nonce'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    showResults(type + ' maintenance completed successfully', response.data);
                    refreshStatus();
                } else {
                    showResults('Error running ' + type + ' maintenance', response.data);
                }
            },
            error: function() {
                showResults('Error running ' + type + ' maintenance', 'AJAX request failed');
            },
            complete: function() {
                button.prop('disabled', false).text('Run ' + type.charAt(0).toUpperCase() + type.slice(1) + ' Maintenance');
            }
        });
    });
    
    // Run individual operations
    $('.run-operation').on('click', function() {
        var operation = $(this).data('operation');
        var button = $(this);
        
        button.prop('disabled', true).text('Running...');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'blackcnote_run_operation',
                operation: operation,
                nonce: '<?php echo wp_create_nonce('blackcnote_maintenance_nonce'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    showResults(operation + ' completed successfully', response.data);
                } else {
                    showResults('Error running ' + operation, response.data);
                }
            },
            error: function() {
                showResults('Error running ' + operation, 'AJAX request failed');
            },
            complete: function() {
                button.prop('disabled', false).text('Run ' + operation.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()));
            }
        });
    });
    
    // Refresh log
    $('.refresh-log').on('click', function() {
        loadMaintenanceLog();
    });
    
    // Load maintenance log
    function loadMaintenanceLog() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'blackcnote_get_maintenance_log',
                nonce: '<?php echo wp_create_nonce('blackcnote_maintenance_nonce'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    $('#maintenance-log').html(response.data);
                } else {
                    $('#maintenance-log').html('<p>Error loading maintenance log</p>');
                }
            },
            error: function() {
                $('#maintenance-log').html('<p>Error loading maintenance log</p>');
            }
        });
    }
    
    // Show results
    function showResults(title, data) {
        var content = '<h3>' + title + '</h3>';
        content += '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
        
        $('#results-content').html(content);
        $('#maintenance-results').show();
        
        // Scroll to results
        $('html, body').animate({
            scrollTop: $('#maintenance-results').offset().top
        }, 500);
    }
    
    // Refresh status
    function refreshStatus() {
        location.reload();
    }
    
    // Load initial log
    loadMaintenanceLog();
});
</script> 