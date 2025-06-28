<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package BlackCnote
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Load Jetpack compatibility file.
 */
if (defined('JETPACK__VERSION')) {
    require get_template_directory() . '/inc/jetpack.php';
} 