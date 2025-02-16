<?php

class DebugUtil {
    private static $logFile = '/var/www/html/ever-remember/logs/debug.log';
    private static $isEnabled = true;

    public static function log($message, $context = []) {
        if (!self::$isEnabled) {
            return;
        }

        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? json_encode($context) : '';
        $logMessage = "[{$timestamp}] {$message} {$contextStr}\n";
        
        error_log($logMessage, 3, self::$logFile);
    }
}