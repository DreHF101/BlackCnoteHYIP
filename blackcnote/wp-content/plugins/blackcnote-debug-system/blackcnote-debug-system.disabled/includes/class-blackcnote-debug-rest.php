<?php
declare(strict_types=1);

class BlackCnote_Debug_REST {
    public static function register_routes() {
        register_rest_route('blackcnote/v1', '/health', [
            'methods' => 'GET',
            'callback' => [self::class, 'get_health'],
            'permission_callback' => '__return_true',
        ]);
    }

    public static function get_health() {
        require_once __DIR__ . '/class-blackcnote-debug-health.php';
        return BlackCnote_Debug_Health::check_services();
    }
} 