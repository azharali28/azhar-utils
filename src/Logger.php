<?php
namespace AzharUtils;

use AzharUtils\PathManager;

class Logger {

    public static function debug(string $context, string $message): void {
        self::write($context, "debug", $message);
    }

    public static function info(string $context, string $message): void {
        self::write($context, "info", $message);
    }

    public static function error(string $context, string $message): void {
        self::write($context, "error", $message);    
    }

    public static function warning(string $context, string $message): void {
        self::write($context, "warning", $message);    
    }

    private static function write(string $context, string $level, string $message): void {
        $app = basename(__DIR__);
        $pid = getmypid();
        $timestamp = date("Y-m-d H:i:s", time());
        $ip = $_SERVER["REMOTE_ADDR"] == "::1" ? "127.0.0.1" : $_SERVER["REMOTE_ADDR"];
        $logMessage = sprintf(
            "%s.%s [%s] %s (%s) %s %s",
            $app,
            $pid,
            $timestamp,
            $ip,
            $context,
            strtoupper($level),
            $message . PHP_EOL
        );
        $messageType = 3;
        $path = dirname(__DIR__) . "/storage/logs";
        $filename = date("Ymd") . ".log";
        $destination = PathManager::ensureFile($path, $filename);
        error_log($logMessage, $messageType, $destination);
    }
}
?>