<?php

namespace Hyiplab\Log;

class Logger
{
    private static string $logFile = __DIR__ . '/../../logs/hyiplab.log';

    public static function info(string $message, array $context = []): void
    {
        self::writeLog('INFO', $message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        self::writeLog('WARNING', $message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::writeLog('ERROR', $message, $context);
    }

    private static function writeLog(string $level, string $message, array $context = []): void
    {
        $date = date('Y-m-d H:i:s');
        $contextStr = $context ? json_encode($context) : '';
        $logLine = "[$date] [$level] $message $contextStr" . PHP_EOL;
        file_put_contents(self::$logFile, $logLine, FILE_APPEND);
    }
}

if (!function_exists('log_info')) {
    function log_info($message, $context = []) {
        \Hyiplab\Log\Logger::info($message, $context);
    }
}
if (!function_exists('log_warning')) {
    function log_warning($message, $context = []) {
        \Hyiplab\Log\Logger::warning($message, $context);
    }
}
if (!function_exists('log_error')) {
    function log_error($message, $context = []) {
        \Hyiplab\Log\Logger::error($message, $context);
    }
} 