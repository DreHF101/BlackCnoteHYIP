<?php
/**
 * BlackCnote Script Checker Admin Page
 * Comprehensive script validation and management interface
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get script checker instance
$script_checker_path = 'C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote\tools\debug\script-checker-integration.php';
$script_checker = null;

if (file_exists($script_checker_path)) {
    require_once $script_checker_path;
    $script_checker = new BlackCnoteScriptChecker();
}

$stats = $script_checker ? $script_checker->getStatistics() : [];
$issues = $script_checker ? $script_checker->getFilesWithIssues() : [];
?>

<div class="wrap">
    <h1><?php esc_html_e('BlackCnote Script Checker', 'blackcnote-debug'); ?></h1>
    <p class="description"><?php esc_html_e('Comprehensive script validation and integrity checking for all BlackCnote project files.', 'blackcnote-debug'); ?></p>
    
    <!-- Notifications Container -->
    <div id="notifications-container"></div>
    
    <!-- Status Overview -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title"><?php esc_html_e('Script Check Status', 'blackcnote-debug'); ?></h2>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="text-center">
                        <h3 class="text-primary" id="script-total-files"><?php echo esc_html($stats['totalFiles'] ?? 0); ?></h3>
                        <small class="text-muted"><?php esc_html_e('Total Files', 'blackcnote-debug'); ?></small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h3 class="text-success" id="script-pass-files"><?php echo esc_html($stats['passFiles'] ?? 0); ?></h3>
                        <small class="text-muted"><?php esc_html_e('Passed', 'blackcnote-debug'); ?></small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h3 class="text-warning" id="script-warning-files"><?php echo esc_html($stats['warningFiles'] ?? 0); ?></h3>
                        <small class="text-muted"><?php esc_html_e('Warnings', 'blackcnote-debug'); ?></small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h3 class="text-danger" id="script-error-files"><?php echo esc_html($stats['errorFiles'] ?? 0); ?></h3>
                        <small class="text-muted"><?php esc_html_e('Errors', 'blackcnote-debug'); ?></small>
                    </div>
                </div>
            </div>
            
            <!-- Overall Status -->
            <div class="alert alert-<?php echo esc_attr($stats['overallStatus'] === 'PASS' ? 'success' : ($stats['overallStatus'] === 'WARNING' ? 'warning' : 'danger')); ?> mt-3">
                <strong><?php esc_html_e('Overall Status:', 'blackcnote-debug'); ?></strong> 
                <span id="script-overall-status"><?php echo esc_html($stats['overallStatus'] ?? 'unknown'); ?></span>
                <?php if ($stats['lastCheck']): ?>
                    <br><small><?php esc_html_e('Last Check:', 'blackcnote-debug'); ?> <span id="script-last-check"><?php echo esc_html($stats['lastCheck']); ?></span></small>
                <?php endif; ?>
            </div>
            
            <!-- Auto-refresh indicator -->
            <div id="auto-refresh-indicator" class="alert alert-info" style="display: none;">
                <small><i class="dashicons dashicons-update"></i> <?php esc_html_e('Auto-refreshing every 30 seconds', 'blackcnote-debug'); ?></small>
            </div>
        </div>
    </div>
    
    <!-- Action Buttons -->
    <div class="card mt-3">
        <div class="card-body">
            <h5><?php esc_html_e('Actions', 'blackcnote-debug'); ?></h5>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-primary run-script-check" data-fix-emojis="false">
                    <i class="dashicons dashicons-search"></i> <?php esc_html_e('Run Check Now', 'blackcnote-debug'); ?>
                </button>
                <button type="button" class="btn btn-warning run-script-check" data-fix-emojis="true">
                    <i class="dashicons dashicons-admin-tools"></i> <?php esc_html_e('Run Check & Fix Emojis', 'blackcnote-debug'); ?>
                </button>
                <button type="button" class="btn btn-info view-script-log">
                    <i class="dashicons dashicons-list-view"></i> <?php esc_html_e('View Detailed Log', 'blackcnote-debug'); ?>
                </button>
                <button type="button" class="btn btn-secondary refresh-script-results">
                    <i class="dashicons dashicons-update"></i> <?php esc_html_e('Refresh Results', 'blackcnote-debug'); ?>
                </button>
            </div>
        </div>
    </div>
    
    <!-- File Type Breakdown -->
    <div class="card mt-3">
        <div class="card-header">
            <h3 class="card-title"><?php esc_html_e('File Type Breakdown', 'blackcnote-debug'); ?></h3>
        </div>
        <div class="card-body">
            <div class="row" id="script-file-breakdown">
                <?php if (!empty($stats['fileTypeBreakdown'])): ?>
                    <?php foreach ($stats['fileTypeBreakdown'] as $type => $typeStats): ?>
                        <div class="col-md-3 mb-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5 class="card-title"><?php echo esc_html($type); ?></h5>
                                    <div class="row">
                                        <div class="col-4">
                                            <small class="text-muted"><?php esc_html_e('Total', 'blackcnote-debug'); ?></small>
                                            <div class="h6"><?php echo esc_html($typeStats['total']); ?></div>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-success"><?php esc_html_e('Pass', 'blackcnote-debug'); ?></small>
                                            <div class="h6 text-success"><?php echo esc_html($typeStats['pass']); ?></div>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-warning"><?php esc_html_e('Issues', 'blackcnote-debug'); ?></small>
                                            <div class="h6 text-warning"><?php echo esc_html($typeStats['errors'] + $typeStats['warnings']); ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <p class="text-muted"><?php esc_html_e('No file type data available. Run a script check to see breakdown.', 'blackcnote-debug'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Issues Table -->
    <div class="card mt-3">
        <div class="card-header">
            <h3 class="card-title"><?php esc_html_e('Files with Issues', 'blackcnote-debug'); ?></h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="script-issues-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('File', 'blackcnote-debug'); ?></th>
                            <th><?php esc_html_e('Type', 'blackcnote-debug'); ?></th>
                            <th><?php esc_html_e('Status', 'blackcnote-debug'); ?></th>
                            <th><?php esc_html_e('Issues', 'blackcnote-debug'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($issues)): ?>
                            <?php foreach ($issues as $issue): ?>
                                <tr class="cursor-pointer" onclick="showIssueDetails(<?php echo htmlspecialchars(json_encode($issue)); ?>)">
                                    <td><?php echo esc_html($issue['name']); ?></td>
                                    <td><?php echo esc_html($issue['type']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $issue['status'] === 'ERROR' ? 'danger' : 'warning'; ?>">
                                            <?php echo esc_html($issue['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo count($issue['errors']) + count($issue['warnings']) + count($issue['unicodeIssues']); ?> issues</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted"><?php esc_html_e('No issues found', 'blackcnote-debug'); ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Configuration -->
    <div class="card mt-3">
        <div class="card-header">
            <h3 class="card-title"><?php esc_html_e('Configuration', 'blackcnote-debug'); ?></h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5><?php esc_html_e('Scheduled Tasks', 'blackcnote-debug'); ?></h5>
                    <p><?php esc_html_e('The script checker runs automatically:', 'blackcnote-debug'); ?></p>
                    <ul>
                        <li><?php esc_html_e('Daily at 2:00 AM', 'blackcnote-debug'); ?></li>
                        <li><?php esc_html_e('Weekly on Sundays at 3:00 AM', 'blackcnote-debug'); ?></li>
                        <li><?php esc_html_e('At system startup (5 minute delay)', 'blackcnote-debug'); ?></li>
                    </ul>
                    <p><small class="text-muted"><?php esc_html_e('To modify schedule, run the scheduler script manually.', 'blackcnote-debug'); ?></small></p>
                </div>
                <div class="col-md-6">
                    <h5><?php esc_html_e('Supported File Types', 'blackcnote-debug'); ?></h5>
                    <ul>
                        <li>PowerShell (.ps1)</li>
                        <li>Batch (.bat, .cmd)</li>
                        <li>Shell (.sh)</li>
                        <li>PHP (.php)</li>
                        <li>JavaScript (.js)</li>
                        <li>Python (.py)</li>
                        <li>And more...</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Issue Details Modal -->
<div class="modal fade" id="issueDetailsModal" tabindex="-1" aria-labelledby="issueDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="issueDetailsModalLabel"><?php esc_html_e('Issue Details', 'blackcnote-debug'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Content will be populated by JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php esc_html_e('Close', 'blackcnote-debug'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Log Modal -->
<div class="modal fade" id="logModal" tabindex="-1" aria-labelledby="logModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logModalLabel"><?php esc_html_e('Script Check Log', 'blackcnote-debug'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <pre style="max-height: 500px; overflow-y: auto;"><?php esc_html_e('Loading log...', 'blackcnote-debug'); ?></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php esc_html_e('Close', 'blackcnote-debug'); ?></button>
            </div>
        </div>
    </div>
</div>

<style>
.cursor-pointer {
    cursor: pointer;
}
.cursor-pointer:hover {
    background-color: #f8f9fa !important;
}
.card {
    margin-bottom: 1rem;
}
.btn-group .btn {
    margin-right: 0.5rem;
}
@media (max-width: 768px) {
    .btn-group .btn {
        margin-bottom: 0.5rem;
        margin-right: 0;
    }
}
</style>

<script>
// Global function for showing issue details
function showIssueDetails(issue) {
    let details = '<h6>File: ' + issue.name + '</h6>';
    details += '<p><strong>Type:</strong> ' + issue.type + '</p>';
    details += '<p><strong>Status:</strong> <span class="badge bg-' + (issue.status === 'ERROR' ? 'danger' : 'warning') + '">' + issue.status + '</span></p>';
    
    if (issue.errors && issue.errors.length > 0) {
        details += '<h6 class="text-danger">Errors:</h6><ul>';
        issue.errors.forEach(function(error) {
            details += '<li>' + error + '</li>';
        });
        details += '</ul>';
    }
    
    if (issue.warnings && issue.warnings.length > 0) {
        details += '<h6 class="text-warning">Warnings:</h6><ul>';
        issue.warnings.forEach(function(warning) {
            details += '<li>' + warning + '</li>';
        });
        details += '</ul>';
    }
    
    if (issue.unicodeIssues && issue.unicodeIssues.length > 0) {
        details += '<h6 class="text-info">Unicode Issues:</h6><ul>';
        issue.unicodeIssues.forEach(function(unicodeIssue) {
            details += '<li>' + unicodeIssue + '</li>';
        });
        details += '</ul>';
    }
    
    document.getElementById('issueDetailsModal').querySelector('.modal-body').innerHTML = details;
    new bootstrap.Modal(document.getElementById('issueDetailsModal')).show();
}
</script> 