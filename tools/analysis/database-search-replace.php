<?php
/**
 * Database Search & Replace for WordPress migration from /blackcnote/ to root
 * Access this file via: http://localhost:8888/db-search-replace.php
 * DELETE THIS FILE AFTER USE!
 */

// Load WordPress
require_once('wp-config.php');
require_once('wp-load.php');
global $wpdb;

$old_url1 = 'http://localhost/blackcnote';
$old_url2 = '/blackcnote/';
$new_url = 'http://localhost:8888/';

$tables = $wpdb->get_col('SHOW TABLES');
$total = 0;
$changed = 0;

foreach ($tables as $table) {
    $columns = $wpdb->get_col("DESC `$table`", 0);
    $rows = $wpdb->get_results("SELECT * FROM `$table`");
    foreach ($rows as $row) {
        $update = false;
        $data = (array)$row;
        $new_data = $data;
        foreach ($columns as $col) {
            if (is_string($data[$col])) {
                $replaced = str_replace([$old_url1, $old_url2], $new_url, $data[$col], $count);
                if ($count > 0) {
                    $new_data[$col] = $replaced;
                    $update = true;
                    $changed++;
                }
            }
        }
        if ($update) {
            $where = [];
            foreach ($columns as $col) {
                $where[$col] = $data[$col];
            }
            $wpdb->update($table, $new_data, $where);
            $total++;
        }
    }
}

echo "<h2>🔄 Search & Replace Complete</h2>";
echo "<p>Rows updated: <strong>$total</strong></p>";
echo "<p>Fields changed: <strong>$changed</strong></p>";
echo "<p><strong>Next steps:</strong></p>";
echo "<ol>";
echo "<li>Test your site frontend and admin.</li>";
echo "<li>Delete this file for security.</li>";
echo "</ol>"; 