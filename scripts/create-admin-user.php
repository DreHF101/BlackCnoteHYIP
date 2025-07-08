<?php
/**
 * Create Administrator User Script
 * 
 * This script creates an administrator user for WordPress
 * Run this script in the WordPress container
 */

// Load WordPress
require_once('/var/www/html/wp-config.php');
require_once('/var/www/html/wp-load.php');

// Check if admin user already exists
$admin_user = get_user_by('login', 'admin');

if ($admin_user) {
    echo "✅ Administrator user 'admin' already exists!\n";
    echo "Username: admin\n";
    echo "Email: " . $admin_user->user_email . "\n";
    echo "Role: " . implode(', ', $admin_user->roles) . "\n";
    exit;
}

// Create administrator user
$user_data = array(
    'user_login'    => 'admin',
    'user_email'    => 'admin@blackcnote.com',
    'user_pass'     => 'password',
    'display_name'  => 'BlackCnote Administrator',
    'role'          => 'administrator',
    'user_nicename' => 'admin'
);

$user_id = wp_insert_user($user_data);

if (is_wp_error($user_id)) {
    echo "❌ Error creating user: " . $user_id->get_error_message() . "\n";
    exit(1);
} else {
    echo "✅ Administrator user created successfully!\n";
    echo "Username: admin\n";
    echo "Password: password\n";
    echo "Email: admin@blackcnote.com\n";
    echo "Role: Administrator\n";
    echo "User ID: $user_id\n";
    echo "\n";
    echo "🔗 You can now login at: http://localhost:8888/wp-admin/\n";
    echo "📧 Login with: admin / password\n";
}

// Verify the user was created
$new_user = get_user_by('id', $user_id);
if ($new_user && in_array('administrator', $new_user->roles)) {
    echo "✅ User verification successful!\n";
} else {
    echo "⚠️  User created but role verification failed\n";
} 