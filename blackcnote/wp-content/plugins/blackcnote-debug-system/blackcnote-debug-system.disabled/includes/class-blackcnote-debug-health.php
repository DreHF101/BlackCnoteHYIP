<?php
declare(strict_types=1);

class BlackCnote_Debug_Health {
    private static function get_services(): array {
        return [
            'wordpress' => [
                'label' => 'WordPress',
                'url' => 'http://wordpress',
            ],
            'react' => [
                'label' => 'React App',
                'url' => 'http://blackcnote-react:5174',
            ],
            'browsersync' => [
                'label' => 'BrowserSync',
                'url' => 'http://blackcnote-browsersync:3000',
            ],
            'docker' => [
                'label' => 'Docker',
                'cmd' => null,
            ],
        ];
    }

    public static function check_services(): array {
        $results = [];
        foreach (self::get_services() as $key => $service) {
            if ($key === 'docker') {
                $results[$key] = ['status' => 'unknown', 'message' => 'Docker CLI not available in container'];
            } else {
                $results[$key] = self::check_url($service['url']);
            }
        }
        return $results;
    }

    private static function check_url($url): array {
        $response = wp_remote_get($url, ['timeout' => 3]);
        if (is_wp_error($response)) {
            return ['status' => 'down', 'message' => $response->get_error_message()];
        }
        $code = wp_remote_retrieve_response_code($response);
        return [
            'status' => ($code >= 200 && $code < 400) ? 'up' : 'down',
            'code' => $code,
        ];
    }
} 