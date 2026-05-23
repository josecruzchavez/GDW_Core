<?php
declare(strict_types=1);

namespace GDW\Core\Util;

use JsonException;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

final class GdwLog
{
    private const DEFAULT_LOG_FILE = 'gdw_core.log';

    /** @var Logger[] */
    private static array $loggers = [];

    /**
     * Log rápido tipo Mage::log()
     *
     * @param mixed $message
     * @param string|null $file  ej: "debug_gdw.log"
     * @param string $level emergency|alert|critical|error|warning|notice|info|debug
     */
    public static function log(mixed $message, ?string $file = null, string $level = 'info'): void
    {
        $name = self::normalizeLogFileName($file);
        $basePath = defined('BP') ? BP : getcwd();
        $path = rtrim((string)$basePath, '/') . '/var/log/' . $name;

        if (!isset(self::$loggers[$name])) {
            $logger = new Logger('gdw_core');
            // DEBUG acepta todos los niveles
            $logger->pushHandler(new StreamHandler($path, Logger::DEBUG));
            self::$loggers[$name] = $logger;
        }

        $logger = self::$loggers[$name];

        if (is_array($message) || is_object($message)) {
            try {
                $message = json_encode(
                    $message,
                    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
                );
            } catch (JsonException) {
                $message = print_r($message, true);
            }
        } else {
            $message = (string) $message;
        }

        $normalizedLevel = strtolower($level);
        $allowedLevels = [
            'emergency',
            'alert',
            'critical',
            'error',
            'warning',
            'notice',
            'info',
            'debug',
        ];

        if (!in_array($normalizedLevel, $allowedLevels, true)) {
            $normalizedLevel = 'info';
        }

        $logger->log($normalizedLevel, $message);
    }

    private static function normalizeLogFileName(?string $file): string
    {
        $candidate = trim((string) $file);
        if ($candidate === '') {
            return self::DEFAULT_LOG_FILE;
        }

        $candidate = basename(str_replace('\\', '/', $candidate));
        $candidate = preg_replace('/[^A-Za-z0-9._-]/', '_', $candidate) ?? '';

        if ($candidate === '' || $candidate === '.' || $candidate === '..') {
            return self::DEFAULT_LOG_FILE;
        }

        return $candidate;
    }

    /*
    Como usar
    \GDW\Core\Util\GdwLog::log('hola mundo');
    \GDW\Core\Util\GdwLog::log(['order' => $orderId], 'orders_debug.log', 'debug');
    */
}