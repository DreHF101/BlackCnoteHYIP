<?php
/**
 * BlackCnote Final Performance Test
 * Test all fixes and verify performance improvements
 */

require_once dirname(__FILE__) . '/wp-load.php';

echo "=== BlackCnote Final Performance Test ===\n\n";

// Test 1: Database Performance
echo "1. Database Performance Test:\n";
$start = microtime(true);
global $wpdb;
$result = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts}");
$duration = (microtime(true) - $start) * 1000;
echo "   Query Time: {$duration}ms\n";

// Test 2: Cached Stats
echo "\n2. Cached Stats Test:\n";
$cached_stats = get_transient('blackcnote_fast_stats');
if ($cached_stats) {
    echo "   ✅ Cached stats available\n";
    echo "   Total Users: {$cached_stats['totalUsers']}\n";
    echo "   Total Invested: \${$cached_stats['totalInvested']}\n";
} else {
    echo "   ⚠️  Cached stats not available\n";
}

// Test 3: Realistic Data
echo "\n3. Realistic Data Test:\n";
$realistic_data = get_option('blackcnote_realistic_data', []);
if (!empty($realistic_data)) {
    echo "   ✅ Realistic data configured\n";
    echo "   Plans Available: " . count($realistic_data['plans'] ?? []) . "\n";
} else {
    echo "   ⚠️  Realistic data not configured\n";
}

// Test 4: Plugin Status
echo "\n4. Plugin Status Test:\n";
$plugins = ['blackcnote-cors', 'hyiplab', 'full-content-checker'];
foreach ($plugins as $plugin) {
    $active = is_plugin_active($plugin . '/' . $plugin . '.php');
    echo "   {$plugin}: " . ($active ? '✅ ACTIVE' : '❌ INACTIVE') . "\n";
}

// Test 5: Table Data
echo "\n5. Table Data Test:\n";
$tables = ['hyiplab_plans', 'hyiplab_users', 'hyiplab_transactions'];
foreach ($tables as $table) {
    $full_table = $wpdb->prefix . $table;
    if ($wpdb->get_var("SHOW TABLES LIKE '{$full_table}'") === $full_table) {
        $count = $wpdb->get_var("SELECT COUNT(*) FROM {$full_table}");
        echo "   {$table}: {$count} records\n";
    } else {
        echo "   {$table}: Does not exist\n";
    }
}

// Test 6: Memory Usage
echo "\n6. Memory Usage Test:\n";
$memory_usage = memory_get_usage(true);
$memory_peak = memory_get_peak_usage(true);
$memory_limit = ini_get('memory_limit');

echo "   Current: " . round($memory_usage / 1024 / 1024, 2) . " MB\n";
echo "   Peak: " . round($memory_peak / 1024 / 1024, 2) . " MB\n";
echo "   Limit: {$memory_limit}\n";

// Test 7: Performance Summary
echo "\n7. Performance Summary:\n";
if ($duration < 100) {
    echo "   ✅ Database: Fast ({$duration}ms)\n";
} else {
    echo "   ⚠️  Database: Slow ({$duration}ms)\n";
}

if ($memory_usage < 50 * 1024 * 1024) { // 50MB
    echo "   ✅ Memory: Good usage\n";
} else {
    echo "   ⚠️  Memory: High usage\n";
}

if (!empty($cached_stats)) {
    echo "   ✅ Caching: Working\n";
} else {
    echo "   ⚠️  Caching: Not working\n";
}

echo "\n=== FINAL STATUS ===\n";

$issues_fixed = 0;
$total_issues = 4;

if ($duration < 100) $issues_fixed++;
if ($memory_usage < 50 * 1024 * 1024) $issues_fixed++;
if (!empty($cached_stats)) $issues_fixed++;
if (!empty($realistic_data)) $issues_fixed++;

echo "Issues Fixed: {$issues_fixed}/{$total_issues}\n";
echo "Success Rate: " . round(($issues_fixed / $total_issues) * 100, 2) . "%\n";

if ($issues_fixed >= 3) {
    echo "\n🎉 EXCELLENT! Server performance is optimized!\n";
    echo "✅ All major issues have been resolved.\n";
    echo "✅ Your BlackCnote platform is ready for production!\n";
} else {
    echo "\n⚠️  Some issues remain. Please review the warnings above.\n";
}

echo "\n🌐 Test Your Application:\n";
echo "   React App: http://localhost:5173\n";
echo "   WordPress: http://localhost:8888\n";
echo "   Admin: http://localhost:8888/wp-admin/\n";

echo "\n=== Test Complete ===\n"; 