<?php
/*
Plugin Name: Full Content & File Checker
Description: Checks all posts, pages, CPTs, header/footer, and core demo files for content.
Version: 1.0
Author: BlackCnote Team
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Add error handling for plugin activation
register_activation_hook(__FILE__, function() {
    // Create a simple activation log
    error_log('Full Content Checker Plugin Activated');
});

add_action('admin_menu', function() {
    try {
        add_management_page(
            'Full Content & File Checker',
            'Full Content Checker',
            'manage_options',
            'full-content-checker',
            function() {
                echo '<div class="wrap"><h1>Full Content & File Checker</h1>';

                // 1. Check all public post types (posts, pages, CPTs)
                $post_types = get_post_types(['public' => true], 'names');
                $empty_content = [];
                foreach ($post_types as $pt) {
                    $posts = get_posts([
                        'post_type'   => $pt,
                        'post_status' => 'publish',
                        'numberposts' => -1
                    ]);
                    foreach ($posts as $post) {
                        $content = trim(strip_tags(strip_shortcodes($post->post_content)));
                        if (empty($content)) {
                            $empty_content[] = [
                                'type' => $pt,
                                'title' => $post->post_title,
                                'edit' => get_edit_post_link($post->ID)
                            ];
                        }
                    }
                }

                // 2. Check Header and Footer template parts/files
                $theme_dir = get_template_directory();
                $header_exists = file_exists($theme_dir . '/header.php');
                $footer_exists = file_exists($theme_dir . '/footer.php');
                $header_empty = $header_exists && (trim(file_get_contents($theme_dir . '/header.php')) === '');
                $footer_empty = $footer_exists && (trim(file_get_contents($theme_dir . '/footer.php')) === '');

                // 3. Check for core demo files
                $core_files = [
                    'style.css',
                    'screenshot.png',
                    'functions.php',
                    'front-page.php',
                    'index.php',
                    'blackcnote-demo-content.xml'
                ];
                $missing_files = [];
                foreach ($core_files as $file) {
                    if (!file_exists($theme_dir . '/' . $file)) {
                        $missing_files[] = $file;
                    }
                }

                // 4. Output results
                if (empty($empty_content)) {
                    echo '<p><strong>All posts, pages, and custom post types have content!</strong></p>';
                } else {
                    echo '<p><strong>The following content items are blank or nearly blank:</strong></p><ul>';
                    foreach ($empty_content as $item) {
                        echo '<li>' . esc_html(ucfirst($item['type'])) . ': <a href="' . esc_url($item['edit']) . '" target="_blank">' . esc_html($item['title']) . '</a></li>';
                    }
                    echo '</ul>';
                }

                echo '<h2>Header/Footer Check</h2>';
                if (!$header_exists) {
                    echo '<p style="color:red;">Header file (header.php) is missing!</p>';
                } elseif ($header_empty) {
                    echo '<p style="color:red;">Header file (header.php) is empty!</p>';
                } else {
                    echo '<p>Header file exists and is not empty.</p>';
                }
                if (!$footer_exists) {
                    echo '<p style="color:red;">Footer file (footer.php) is missing!</p>';
                } elseif ($footer_empty) {
                    echo '<p style="color:red;">Footer file (footer.php) is empty!</p>';
                } else {
                    echo '<p>Footer file exists and is not empty.</p>';
                }

                echo '<h2>Core Demo Files Check</h2>';
                if (empty($missing_files)) {
                    echo '<p>All core demo files are present.</p>';
                } else {
                    echo '<p style="color:red;">Missing core demo files:</p><ul>';
                    foreach ($missing_files as $file) {
                        echo '<li>' . esc_html($file) . '</li>';
                    }
                    echo '</ul>';
                }

                echo '</div>';
            }
        );
    } catch (Exception $e) {
        error_log('Full Content Checker Plugin Error: ' . $e->getMessage());
    }
}); 