<?php
/**
 * BlackCnoteDebugSystem - Standalone Version
 * Provides comprehensive logging and monitoring for the entire BlackCnote project.
 * Can be used by WordPress, CLI, or as a background daemon.
 */

declare(strict_types=1);

namespace BlackCnote\Log;

class BlackCnoteDebugSystem {
    private static $instance = null;
    private $log_file;
    private $debug_enabled = true;
    private $log_level = 'ALL';
    private $environment_info = null;
    private $base_path;

    public static function getInstance(array $config = []) {
        if (self::$instance === null) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    public function __construct(array $config = []) {
        $this->base_path = $config['base_path'] ?? dirname(__DIR__, 3);
        $this->log_file = $config['log_file'] ?? $this->base_path . '/logs/blackcnote-debug.log';
        $this->debug_enabled = $config['debug_enabled'] ?? true;
        $this->log_level = $config['log_level'] ?? 'ALL';
        $this->detectEnvironment();
        $this->setupErrorHandling();
        $this->setupExceptionHandling();
        $this->setupShutdownHandling();
        $this->log('BlackCnote Debug System (standalone) initialized', 'SYSTEM');
    }

    private function detectEnvironment() {
        $this->environment_info = [
            'php_version' => PHP_VERSION,
            'os' => PHP_OS,
            'base_path' => $this->base_path,
            'log_file' => $this->log_file,
            'debug_enabled' => $this->debug_enabled,
            'log_level' => $this->log_level,
            'timestamp' => date('c'),
        ];
    }

    private function setupErrorHandling() {
        set_error_handler([$this, 'handleError']);
    }
    private function setupExceptionHandling() {
        set_exception_handler([$this, 'handleException']);
    }
    private function setupShutdownHandling() {
        register_shutdown_function([$this, 'handleShutdown']);
    }

    public function handleError($errno, $errstr, $errfile, $errline, $errcontext = null) {
        $this->log("Error [$errno]: $errstr in $errfile on line $errline", 'ERROR');
    }
    public function handleException($exception) {
        $this->log('Uncaught Exception: ' . $exception->getMessage(), 'ERROR', [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
    public function handleShutdown() {
        $error = error_get_last();
        if ($error !== null) {
            $this->log('Shutdown Error: ' . print_r($error, true), 'ERROR');
        }
    }

    public function log($message, $level = 'INFO', $context = []) {
        if (!$this->debug_enabled) return;
        $entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'level' => $level,
            'message' => $message,
            'context' => $context
        ];
        file_put_contents($this->log_file, json_encode($entry) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    public function getLogFilePath() {
        return $this->log_file;
    }
    public function clearLog() {
        if (file_exists($this->log_file)) {
            unlink($this->log_file);
        }
    }
    public function getLogFileSize() {
        return file_exists($this->log_file) ? filesize($this->log_file) : 0;
    }
    public function getEnvironmentInfo() {
        return $this->environment_info;
    }
} 