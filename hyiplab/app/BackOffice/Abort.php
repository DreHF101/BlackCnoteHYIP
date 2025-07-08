<?php

namespace Hyiplab\BackOffice;

class Abort extends CoreController
{

    public $code;
    public $message;
    public $viewPath;

    private $errors = [
        400 => [
            'title'   => 'Bad Request',
            'message' => '400 Bad Request'
        ],
        401 => [
            'title'   => 'Unauthorized',
            'message' => '401 Unauthorized'
        ],
        402 => [
            'title'   => 'Payment Required',
            'message' => '402 Payment Required'
        ],
        403 => [
            'title'   => 'Forbidden',
            'message' => '403 Forbidden'
        ],
        404 => [
            'title'   => 'Not Found',
            'message' => '404 Page Not Found'
        ],
        405 => [
            'title'   => 'Method Not Allowed',
            'message' => '405 Method Not Allowed'
        ],
        500 => [
            'title'   => 'Internal Server Error',
            'message' => '500 Internal Server Error'
        ],
        502 => [
            'title'   => 'Bad Gateway',
            'message' => '502 Bad Gateway'
        ],
        503 => [
            'title'   => 'Service Unavailable',
            'message' => '503 Service Unavailable'
        ]
    ];

    public function __construct($code, $message = null)
    {
        $this->viewPath = HYIPLAB_ROOT . 'views';
        $this->code = $this->validateErrorCode($code);
        $this->message = $this->sanitizeMessage($message);
    }

    /**
     * Validate error code
     * @param int $code Error code
     * @return int Valid error code
     */
    private function validateErrorCode($code)
    {
        $code = (int) $code;
        
        if (!array_key_exists($code, $this->errors)) {
            // Log invalid error code
            error_log("HyipLab Abort: Invalid error code {$code} provided, defaulting to 500");
            return 500;
        }
        
        return $code;
    }

    /**
     * Sanitize error message
     * @param string|null $message Error message
     * @return string Sanitized message
     */
    private function sanitizeMessage($message)
    {
        if ($message === null) {
            return '';
        }
        
        // Sanitize message for security
        $message = sanitize_text_field($message);
        
        // Limit message length
        if (strlen($message) > 500) {
            $message = substr($message, 0, 497) . '...';
        }
        
        return $message;
    }

    /**
     * Abort with error handling
     */
    public function abort()
    {
        $error = $this->errors[$this->code];
        $message = $this->message ?: $error['message'];
        
        // Log the error for debugging
        $this->logError($error, $message);
        
        // Set proper headers
        $this->setHeaders();
        
        // Try to render custom error page
        if ($this->renderCustomErrorPage($error, $message)) {
            return;
        }
        
        // Fallback to WordPress error handling
        $this->renderWordPressError($error, $message);
    }

    /**
     * Log error for debugging
     * @param array $error Error information
     * @param string $message Error message
     */
    private function logError($error, $message)
    {
        $log_message = sprintf(
            'HyipLab Abort: %s - %s (Code: %d, Message: %s)',
            $error['title'],
            $error['message'],
            $this->code,
            $message
        );
        
        error_log($log_message);
        
        // Log to WordPress debug log if available
        if (function_exists('error_log')) {
            error_log($log_message);
        }
    }

    /**
     * Set proper HTTP headers
     */
    private function setHeaders()
    {
        // Set status header
        if (function_exists('status_header')) {
            status_header($this->code);
        } else {
            http_response_code($this->code);
        }
        
        // Set no-cache headers
        if (function_exists('nocache_headers')) {
            nocache_headers();
        } else {
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
        }
        
        // Set content type
        header('Content-Type: text/html; charset=UTF-8');
    }

    /**
     * Try to render custom error page
     * @param array $error Error information
     * @param string $message Error message
     * @return bool True if custom page was rendered
     */
    private function renderCustomErrorPage($error, $message)
    {
        $error_file = $this->viewPath . '/errors/' . $this->code . '.php';
        
        if (file_exists($error_file)) {
            try {
                $pageTitle = $error['title'];
                
                // Include the error template
                if (function_exists('hyiplab_include')) {
                    hyiplab_include('errors/' . $this->code, compact('pageTitle', 'message'));
                } else {
                    // Fallback include
                    extract(compact('pageTitle', 'message'));
                    include $error_file;
                }
                
                return true;
            } catch (\Exception $e) {
                // Log template error
                error_log("HyipLab Abort: Error rendering custom error page: " . $e->getMessage());
                return false;
            }
        }
        
        return false;
    }

    /**
     * Render WordPress error page
     * @param array $error Error information
     * @param string $message Error message
     */
    private function renderWordPressError($error, $message)
    {
        if (function_exists('wp_die')) {
            wp_die($message, $error['title'], $this->code);
        } else {
            // Fallback error display
            echo '<!DOCTYPE html>';
            echo '<html><head>';
            echo '<title>' . htmlspecialchars($error['title']) . '</title>';
            echo '<style>';
            echo 'body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }';
            echo '.error { color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 20px; border-radius: 5px; }';
            echo '</style>';
            echo '</head><body>';
            echo '<div class="error">';
            echo '<h1>' . htmlspecialchars($error['title']) . '</h1>';
            echo '<p>' . htmlspecialchars($message) . '</p>';
            echo '</div>';
            echo '</body></html>';
        }
        
        exit;
    }

    /**
     * Get error information
     * @return array Error information
     */
    public function getErrorInfo()
    {
        return [
            'code' => $this->code,
            'message' => $this->message,
            'error' => $this->errors[$this->code] ?? null
        ];
    }

    /**
     * Check if error code is valid
     * @param int $code Error code
     * @return bool
     */
    public static function isValidErrorCode($code)
    {
        $instance = new self($code);
        return array_key_exists($code, $instance->errors);
    }
}
