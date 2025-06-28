<?php
/**
 * BlackCnote Script Checker Integration
 * PHP integration for the BlackCnote Debug System/Plugin
 * 
 * This file provides functions to:
 * - Read script check results
 * - Trigger script checks
 * - Display results in admin dashboard
 * - Send notifications
 */

declare(strict_types=1);

class BlackCnoteScriptChecker {
    
    private string $projectRoot;
    private string $logFile;
    private string $jsonFile;
    private string $checkerScript;
    
    public function __construct() {
        $this->projectRoot = 'C:\Users\CASH AMERICA PAWN\Desktop\BlackCnote';
        $this->logFile = $this->projectRoot . '\logs\script-check.log';
        $this->jsonFile = $this->projectRoot . '\logs\script-check.json';
        $this->checkerScript = $this->projectRoot . '\tools\debug\check-all-scripts.ps1';
    }
    
    /**
     * Get the latest script check results
     */
    public function getResults(): array {
        $results = [
            'summary' => null,
            'files' => [],
            'lastCheck' => null,
            'status' => 'unknown'
        ];
        
        if (file_exists($this->jsonFile)) {
            try {
                $jsonData = json_decode(file_get_contents($this->jsonFile), true);
                if ($jsonData) {
                    $results['summary'] = $jsonData['summary'] ?? null;
                    $results['files'] = $jsonData['results'] ?? [];
                    $results['lastCheck'] = $jsonData['summary']['Timestamp'] ?? null;
                    $results['status'] = $jsonData['summary']['OverallStatus'] ?? 'unknown';
                }
            } catch (Exception $e) {
                error_log("Failed to parse script check JSON: " . $e->getMessage());
            }
        }
        
        return $results;
    }
    
    /**
     * Get detailed log content
     */
    public function getLogContent(): string {
        if (file_exists($this->logFile)) {
            return file_get_contents($this->logFile);
        }
        return "No log file found.";
    }
    
    /**
     * Trigger a new script check
     */
    public function runCheck(bool $fixEmojis = false): array {
        $result = [
            'success' => false,
            'message' => '',
            'exitCode' => -1
        ];
        
        if (!file_exists($this->checkerScript)) {
            $result['message'] = "Script checker not found at: " . $this->checkerScript;
            return $result;
        }
        
        $command = 'powershell.exe -ExecutionPolicy Bypass -File "' . $this->checkerScript . '"';
        if ($fixEmojis) {
            $command .= ' -FixEmojis';
        }
        
        $output = [];
        $exitCode = 0;
        
        exec($command . ' 2>&1', $output, $exitCode);
        
        $result['success'] = ($exitCode === 0 || $exitCode === 2); // 0 = pass, 2 = warnings
        $result['message'] = implode("\n", $output);
        $result['exitCode'] = $exitCode;
        
        return $result;
    }
    
    /**
     * Get statistics for dashboard
     */
    public function getStatistics(): array {
        $results = $this->getResults();
        $stats = [
            'totalFiles' => 0,
            'errorFiles' => 0,
            'warningFiles' => 0,
            'passFiles' => 0,
            'overallStatus' => 'unknown',
            'lastCheck' => null,
            'fileTypeBreakdown' => []
        ];
        
        if ($results['summary']) {
            $stats['totalFiles'] = $results['summary']['TotalFiles'] ?? 0;
            $stats['errorFiles'] = $results['summary']['ErrorFiles'] ?? 0;
            $stats['warningFiles'] = $results['summary']['WarningFiles'] ?? 0;
            $stats['passFiles'] = $results['summary']['PassFiles'] ?? 0;
            $stats['overallStatus'] = $results['summary']['OverallStatus'] ?? 'unknown';
            $stats['lastCheck'] = $results['summary']['Timestamp'] ?? null;
        }
        
        // Calculate file type breakdown
        if (!empty($results['files'])) {
            $fileTypes = [];
            foreach ($results['files'] as $file) {
                $type = $file['FileType'] ?? 'unknown';
                if (!isset($fileTypes[$type])) {
                    $fileTypes[$type] = [
                        'total' => 0,
                        'errors' => 0,
                        'warnings' => 0,
                        'pass' => 0
                    ];
                }
                $fileTypes[$type]['total']++;
                
                switch ($file['Status']) {
                    case 'ERROR':
                        $fileTypes[$type]['errors']++;
                        break;
                    case 'WARNING':
                        $fileTypes[$type]['warnings']++;
                        break;
                    case 'PASS':
                        $fileTypes[$type]['pass']++;
                        break;
                }
            }
            $stats['fileTypeBreakdown'] = $fileTypes;
        }
        
        return $stats;
    }
    
    /**
     * Get files with issues
     */
    public function getFilesWithIssues(): array {
        $results = $this->getResults();
        $issues = [];
        
        if (!empty($results['files'])) {
            foreach ($results['files'] as $file) {
                if ($file['Status'] !== 'PASS') {
                    $issues[] = [
                        'name' => $file['Name'],
                        'type' => $file['FileType'],
                        'status' => $file['Status'],
                        'errors' => $file['Errors'] ?? [],
                        'warnings' => $file['Warnings'] ?? [],
                        'unicodeIssues' => $file['UnicodeIssues'] ?? [],
                        'path' => $file['File']
                    ];
                }
            }
        }
        
        return $issues;
    }
    
    /**
     * Generate HTML dashboard widget
     */
    public function generateDashboardWidget(): string {
        $stats = $this->getStatistics();
        $issues = $this->getFilesWithIssues();
        
        $statusColor = match($stats['overallStatus']) {
            'PASS' => 'success',
            'WARNING' => 'warning',
            'ERROR' => 'danger',
            default => 'secondary'
        };
        
        $html = '<div class="card">';
        $html .= '<div class="card-header">';
        $html .= '<h5 class="card-title mb-0">Script Integrity Checker</h5>';
        $html .= '</div>';
        $html .= '<div class="card-body">';
        
        // Status summary
        $html .= '<div class="row mb-3">';
        $html .= '<div class="col-md-3">';
        $html .= '<div class="text-center">';
        $html .= '<h3 class="text-' . $statusColor . '">' . $stats['totalFiles'] . '</h3>';
        $html .= '<small class="text-muted">Total Files</small>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="col-md-3">';
        $html .= '<div class="text-center">';
        $html .= '<h3 class="text-success">' . $stats['passFiles'] . '</h3>';
        $html .= '<small class="text-muted">Passed</small>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="col-md-3">';
        $html .= '<div class="text-center">';
        $html .= '<h3 class="text-warning">' . $stats['warningFiles'] . '</h3>';
        $html .= '<small class="text-muted">Warnings</small>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="col-md-3">';
        $html .= '<div class="text-center">';
        $html .= '<h3 class="text-danger">' . $stats['errorFiles'] . '</h3>';
        $html .= '<small class="text-muted">Errors</small>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        
        // Overall status
        $html .= '<div class="alert alert-' . $statusColor . ' mb-3">';
        $html .= '<strong>Overall Status:</strong> ' . $stats['overallStatus'];
        if ($stats['lastCheck']) {
            $html .= '<br><small>Last Check: ' . $stats['lastCheck'] . '</small>';
        }
        $html .= '</div>';
        
        // Action buttons
        $html .= '<div class="mb-3">';
        $html .= '<button type="button" class="btn btn-primary btn-sm" onclick="runScriptCheck()">Run Check Now</button>';
        $html .= '<button type="button" class="btn btn-warning btn-sm ms-2" onclick="runScriptCheck(true)">Run Check & Fix Emojis</button>';
        $html .= '<button type="button" class="btn btn-info btn-sm ms-2" onclick="viewDetailedLog()">View Detailed Log</button>';
        $html .= '</div>';
        
        // Issues list
        if (!empty($issues)) {
            $html .= '<h6>Files with Issues:</h6>';
            $html .= '<div class="table-responsive">';
            $html .= '<table class="table table-sm">';
            $html .= '<thead><tr><th>File</th><th>Type</th><th>Status</th><th>Issues</th></tr></thead>';
            $html .= '<tbody>';
            
            foreach ($issues as $issue) {
                $issueCount = count($issue['errors']) + count($issue['warnings']) + count($issue['unicodeIssues']);
                $html .= '<tr>';
                $html .= '<td>' . htmlspecialchars($issue['name']) . '</td>';
                $html .= '<td>' . htmlspecialchars($issue['type']) . '</td>';
                $html .= '<td><span class="badge bg-' . ($issue['status'] === 'ERROR' ? 'danger' : 'warning') . '">' . $issue['status'] . '</span></td>';
                $html .= '<td>' . $issueCount . ' issues</td>';
                $html .= '</tr>';
            }
            
            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div>';
        }
        
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Send notification (if configured)
     */
    public function sendNotification(array $results): bool {
        // This would integrate with your existing notification system
        // For now, just log the notification
        $message = "Script check completed: " . ($results['summary']['OverallStatus'] ?? 'unknown');
        error_log($message);
        return true;
    }
}

// AJAX handler for WordPress admin
if (isset($_POST['action']) && $_POST['action'] === 'blackcnote_script_check') {
    header('Content-Type: application/json');
    
    $checker = new BlackCnoteScriptChecker();
    $fixEmojis = isset($_POST['fix_emojis']) && $_POST['fix_emojis'] === 'true';
    
    $result = $checker->runCheck($fixEmojis);
    
    echo json_encode([
        'success' => $result['success'],
        'message' => $result['message'],
        'exitCode' => $result['exitCode']
    ]);
    
    exit;
}

// AJAX handler for getting results
if (isset($_POST['action']) && $_POST['action'] === 'blackcnote_get_script_results') {
    header('Content-Type: application/json');
    
    $checker = new BlackCnoteScriptChecker();
    $results = $checker->getResults();
    
    echo json_encode($results);
    exit;
}
?> 