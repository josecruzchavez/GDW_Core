<?php
namespace GDW\Core\Util;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

final class GdwLog
{
    /** @var Logger[] */
    private static array $loggers = [];

    /**
     * Log rápido tipo Mage::log()
     *
     * @param mixed $message
     * @param string|null $file  ej: "debug_gdw.log"
     * @param string $level emergency|alert|critical|error|warning|notice|info|debug
     */
    public static function log($message, ?string $file = null, string $level = 'info'): void
    {
        $name = $file ? ltrim($file, '/') : 'gdw_core.log';
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
            $encoded = json_encode(
                $message,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );
            $message = ($encoded !== false) ? $encoded : print_r($message, true);
        } else {
            $message = (string)$message;
        }

        $map = [
            'emergency' => Logger::EMERGENCY,
            'alert'     => Logger::ALERT,
            'critical'  => Logger::CRITICAL,
            'error'     => Logger::ERROR,
            'warning'   => Logger::WARNING,
            'notice'    => Logger::NOTICE,
            'info'      => Logger::INFO,
            'debug'     => Logger::DEBUG,
        ];

        $monologLevel = $map[$level] ?? Logger::INFO;
        $logger->log($monologLevel, $message);
    }

    /*
    Como usar
    \GDW\Core\Util\GdwLog::log('hola mundo');
    \GDW\Core\Util\GdwLog::log(['order' => $orderId], 'orders_debug.log', 'debug');
    */
}