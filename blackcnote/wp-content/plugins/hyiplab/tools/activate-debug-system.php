<?php
/**
 * BlackCnote Debug System Activation Script
 * Loads the debug system and runs comprehensive checks
 */

echo "BlackCnote Debug System Activation\n";
echo "==================================\n\n";

// Load WordPress
require_once __DIR__ . '/../../blackcnote/wp-config.php';

// Load debug system
if (file_exists(WP_CONTENT_DIR . '/debug-system.php')) {
    require_once WP_CONTENT_DIR . '/debug-system.php';
    echo "✓ Debug system loaded\n";
} else {
    echo "✗ Debug system not found\n";
    exit(1);
}

// Load debug monitor
if (file_exists(WP_CONTENT_DIR . '/debug-monitor.php')) {
    require_once WP_CONTENT_DIR . '/debug-monitor.php';
    echo "✓ Debug monitor loaded\n";
} else {
    echo "✗ Debug monitor not found\n";
    exit(1);
}

// Initialize debug system
if (class_exists('BlackCnoteDebugSystem')) {
    $debug_system = BlackCnoteDebugSystem::getInstance();
    echo "✓ Debug system initialized\n";
} else {
    echo "✗ Failed to initialize debug system\n";
    exit(1);
}

// Run comprehensive system check
echo "\nRunning comprehensive system check...\n";
$monitor = new BlackCnoteDebugMonitor();
$monitor->runSystemCheck();

echo "\n✓ Debug system activation completed\n";
echo "Check the debug log at: " . $debug_system->getLogFilePath() . "\n";

// Test the debug system
echo "\nTesting debug system...\n";
blackcnote_log('Debug system test message', 'INFO', ['test' => true]);
blackcnote_log_hyiplab('HYIPLab debug test', 'INFO', ['test' => true]);
blackcnote_log_theme('Theme debug test', 'INFO', ['test' => true]);

echo "✓ Debug system test completed\n";
echo "\nDebug system is now active and monitoring all components!\n";
?> 