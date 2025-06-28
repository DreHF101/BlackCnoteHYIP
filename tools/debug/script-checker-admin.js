/**
 * BlackCnote Script Checker Admin Interface
 * JavaScript functionality for the admin dashboard
 */

(function($) {
    'use strict';
    
    // Global variables
    let scriptCheckerRunning = false;
    let refreshInterval = null;
    
    /**
     * Initialize the script checker interface
     */
    function initScriptChecker() {
        // Bind event handlers
        $(document).on('click', '.run-script-check', function(e) {
            e.preventDefault();
            const fixEmojis = $(this).data('fix-emojis') || false;
            runScriptCheck(fixEmojis);
        });
        
        $(document).on('click', '.view-script-log', function(e) {
            e.preventDefault();
            viewDetailedLog();
        });
        
        $(document).on('click', '.refresh-script-results', function(e) {
            e.preventDefault();
            refreshResults();
        });
        
        // Auto-refresh results every 30 seconds if there are issues
        startAutoRefresh();
    }
    
    /**
     * Run script check
     */
    function runScriptCheck(fixEmojis = false) {
        if (scriptCheckerRunning) {
            showNotification('Script check already running...', 'warning');
            return;
        }
        
        scriptCheckerRunning = true;
        showNotification('Running script check...', 'info');
        
        // Update UI
        $('.run-script-check').prop('disabled', true).text('Running...');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'blackcnote_script_check',
                fix_emojis: fixEmojis
            },
            success: function(response) {
                if (response.success) {
                    showNotification('Script check completed successfully!', 'success');
                    refreshResults();
                } else {
                    showNotification('Script check failed: ' + response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                showNotification('Script check failed: ' + error, 'error');
            },
            complete: function() {
                scriptCheckerRunning = false;
                $('.run-script-check').prop('disabled', false).text('Run Check Now');
            }
        });
    }
    
    /**
     * Refresh results
     */
    function refreshResults() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'blackcnote_get_script_results'
            },
            success: function(response) {
                updateDashboard(response);
            },
            error: function(xhr, status, error) {
                showNotification('Failed to refresh results: ' + error, 'error');
            }
        });
    }
    
    /**
     * Update dashboard with new results
     */
    function updateDashboard(data) {
        if (!data.summary) {
            return;
        }
        
        const summary = data.summary;
        
        // Update statistics
        $('#script-total-files').text(summary.TotalFiles || 0);
        $('#script-pass-files').text(summary.PassFiles || 0);
        $('#script-warning-files').text(summary.WarningFiles || 0);
        $('#script-error-files').text(summary.ErrorFiles || 0);
        
        // Update overall status
        const status = summary.OverallStatus || 'unknown';
        const statusClass = getStatusClass(status);
        $('#script-overall-status')
            .removeClass('alert-success alert-warning alert-danger alert-secondary')
            .addClass('alert-' + statusClass)
            .text(status);
        
        // Update last check time
        if (summary.Timestamp) {
            $('#script-last-check').text(summary.Timestamp);
        }
        
        // Update issues table
        updateIssuesTable(data.files || []);
        
        // Update file type breakdown
        updateFileTypeBreakdown(data.files || []);
        
        // Show/hide auto-refresh based on issues
        if (summary.ErrorFiles > 0 || summary.WarningFiles > 0) {
            startAutoRefresh();
        } else {
            stopAutoRefresh();
        }
    }
    
    /**
     * Update issues table
     */
    function updateIssuesTable(files) {
        const issues = files.filter(file => file.Status !== 'PASS');
        const tbody = $('#script-issues-table tbody');
        tbody.empty();
        
        if (issues.length === 0) {
            tbody.append('<tr><td colspan="4" class="text-center text-muted">No issues found</td></tr>');
            return;
        }
        
        issues.forEach(function(issue) {
            const issueCount = (issue.Errors ? issue.Errors.length : 0) + 
                              (issue.Warnings ? issue.Warnings.length : 0) + 
                              (issue.UnicodeIssues ? issue.UnicodeIssues.length : 0);
            
            const row = $('<tr>');
            row.append('<td>' + escapeHtml(issue.Name) + '</td>');
            row.append('<td>' + escapeHtml(issue.FileType) + '</td>');
            row.append('<td><span class="badge bg-' + (issue.Status === 'ERROR' ? 'danger' : 'warning') + '">' + issue.Status + '</span></td>');
            row.append('<td>' + issueCount + ' issues</td>');
            
            // Add click handler to show details
            row.addClass('cursor-pointer').on('click', function() {
                showIssueDetails(issue);
            });
            
            tbody.append(row);
        });
    }
    
    /**
     * Update file type breakdown
     */
    function updateFileTypeBreakdown(files) {
        const breakdown = {};
        
        files.forEach(function(file) {
            const type = file.FileType || 'unknown';
            if (!breakdown[type]) {
                breakdown[type] = { total: 0, errors: 0, warnings: 0, pass: 0 };
            }
            
            breakdown[type].total++;
            
            switch (file.Status) {
                case 'ERROR':
                    breakdown[type].errors++;
                    break;
                case 'WARNING':
                    breakdown[type].warnings++;
                    break;
                case 'PASS':
                    breakdown[type].pass++;
                    break;
            }
        });
        
        const container = $('#script-file-breakdown');
        container.empty();
        
        Object.keys(breakdown).forEach(function(type) {
            const stats = breakdown[type];
            const card = $('<div class="col-md-3 mb-3">');
            card.append(`
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">${escapeHtml(type)}</h5>
                        <div class="row">
                            <div class="col-4">
                                <small class="text-muted">Total</small>
                                <div class="h6">${stats.total}</div>
                            </div>
                            <div class="col-4">
                                <small class="text-success">Pass</small>
                                <div class="h6 text-success">${stats.pass}</div>
                            </div>
                            <div class="col-4">
                                <small class="text-warning">Issues</small>
                                <div class="h6 text-warning">${stats.errors + stats.warnings}</div>
                            </div>
                        </div>
                    </div>
                </div>
            `);
            container.append(card);
        });
    }
    
    /**
     * Show issue details modal
     */
    function showIssueDetails(issue) {
        let details = '<h6>File: ' + escapeHtml(issue.Name) + '</h6>';
        details += '<p><strong>Type:</strong> ' + escapeHtml(issue.FileType) + '</p>';
        details += '<p><strong>Status:</strong> <span class="badge bg-' + (issue.Status === 'ERROR' ? 'danger' : 'warning') + '">' + issue.Status + '</span></p>';
        
        if (issue.Errors && issue.Errors.length > 0) {
            details += '<h6 class="text-danger">Errors:</h6><ul>';
            issue.Errors.forEach(function(error) {
                details += '<li>' + escapeHtml(error) + '</li>';
            });
            details += '</ul>';
        }
        
        if (issue.Warnings && issue.Warnings.length > 0) {
            details += '<h6 class="text-warning">Warnings:</h6><ul>';
            issue.Warnings.forEach(function(warning) {
                details += '<li>' + escapeHtml(warning) + '</li>';
            });
            details += '</ul>';
        }
        
        if (issue.UnicodeIssues && issue.UnicodeIssues.length > 0) {
            details += '<h6 class="text-info">Unicode Issues:</h6><ul>';
            issue.UnicodeIssues.forEach(function(unicodeIssue) {
                details += '<li>' + escapeHtml(unicodeIssue) + '</li>';
            });
            details += '</ul>';
        }
        
        // Show modal
        $('#issueDetailsModal .modal-body').html(details);
        $('#issueDetailsModal').modal('show');
    }
    
    /**
     * View detailed log
     */
    function viewDetailedLog() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'blackcnote_get_script_log'
            },
            success: function(response) {
                $('#logModal .modal-body pre').text(response);
                $('#logModal').modal('show');
            },
            error: function(xhr, status, error) {
                showNotification('Failed to load log: ' + error, 'error');
            }
        });
    }
    
    /**
     * Start auto-refresh
     */
    function startAutoRefresh() {
        if (refreshInterval) {
            return;
        }
        
        refreshInterval = setInterval(function() {
            refreshResults();
        }, 30000); // 30 seconds
        
        $('#auto-refresh-indicator').show();
    }
    
    /**
     * Stop auto-refresh
     */
    function stopAutoRefresh() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
            refreshInterval = null;
        }
        
        $('#auto-refresh-indicator').hide();
    }
    
    /**
     * Show notification
     */
    function showNotification(message, type) {
        const alertClass = type === 'error' ? 'danger' : type;
        const alert = $('<div class="alert alert-' + alertClass + ' alert-dismissible fade show" role="alert">');
        alert.html(message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>');
        
        $('#notifications-container').append(alert);
        
        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            alert.alert('close');
        }, 5000);
    }
    
    /**
     * Get status class for Bootstrap
     */
    function getStatusClass(status) {
        switch (status) {
            case 'PASS':
                return 'success';
            case 'WARNING':
                return 'warning';
            case 'ERROR':
                return 'danger';
            default:
                return 'secondary';
        }
    }
    
    /**
     * Escape HTML
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    /**
     * Initialize when document is ready
     */
    $(document).ready(function() {
        initScriptChecker();
        
        // Initial load
        refreshResults();
    });
    
    // Make functions globally available
    window.BlackCnoteScriptChecker = {
        runScriptCheck: runScriptCheck,
        refreshResults: refreshResults,
        viewDetailedLog: viewDetailedLog
    };
    
})(jQuery); 