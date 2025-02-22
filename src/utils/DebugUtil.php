<?php

class DebugUtil {
    private static $isEnabled = true;

    public static function log($message, $context = []) {
        if (!self::$isEnabled) {
            return;
        }

        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? json_encode($context) : '';
        $logMessage = "[{$timestamp}] {$message} {$contextStr}\n";
        
        LogUtil::log('debug', $logMessage);
    }
}