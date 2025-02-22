<?php

// Set timezone to UTC
date_default_timezone_set('UTC');

// Custom error handler
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    $message = "Error [$errno]: $errstr in $errfile on line $errline";
    LogUtil::log('error', $message);
    // You can also display a user-friendly error message or halt execution if needed
});

// Custom exception handler
set_exception_handler(function ($exception) {
    $message = "Uncaught exception: " . $exception->getMessage();
    LogUtil::log('error', $message);
    // You can also display a user-friendly error message or halt execution if needed
});

class LogUtil {
    private static $logFiles = [
        'error' => '/var/www/html/ever-remember/logs/error.log',
        'content' => '/var/www/html/ever-remember/logs/content.log',
        'session' => '/var/www/html/ever-remember/logs/session.log',
        'payment' => '/var/www/html/ever-remember/logs/payment.log',
        'debug' => '/var/www/html/ever-remember/logs/debug.log',
    ];

    public static function initializeLogFiles() {
        foreach (self::$logFiles as $file) {
            if (!file_exists($file)) {
                touch($file);
                chmod($file, 0664); // Set permissions to read and write for owner and group
                chown($file, 'yourusername'); // Replace 'yourusername' with your actual username
                chgrp($file, 'www-data'); // Replace 'www-data' with the Apache group
            }
        }
    }

    public static function log($type, $message) {
        if (!isset(self::$logFiles[$type])) {
            throw new InvalidArgumentException("Invalid log type: {$type}");
        }

        // Ensure the message does not already end with a newline
        $message = '[Time: UTC ' . date('Y-m-d H:i:s') . '] ' . rtrim($message, PHP_EOL);
        error_log($message . PHP_EOL, 3, self::$logFiles[$type]);
    }
}


// Initialize log files
LogUtil::initializeLogFiles();