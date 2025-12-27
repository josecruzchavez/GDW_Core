<?php
namespace GDW\Core\Util;

final class Val
{
    /**
     * Convert textarea value to array (one line = one value)
     */
    public static function textareaToArray($value): array
    {
        $string = self::string($value);
        return array_values(array_filter(array_map('trim', explode(PHP_EOL, $string))));
    }

    /**
     * Ensure array from mixed
     */
    public static function array($value): array
    {
        if (is_array($value)) return $value;
        if ($value === null) return [];
        if ($value instanceof \Traversable) return iterator_to_array($value);
        return [$value];
    }

    /**
     * Convert object/array/scalar to array (shallow)
     */
    public static function toArray($value): array
    {
        if (is_array($value)) return $value;
        if (is_object($value)) return get_object_vars($value);
        if ($value === null) return [];
        return [$value];
    }

    /**
     * Safe JSON encode
     */
    public static function json($value, bool $pretty = false): string
    {
        $flags = JSON_UNESCAPED_UNICODE;
        if ($pretty) {
            $flags |= JSON_PRETTY_PRINT;
        }

        return json_encode($value, $flags) ?: '';
    }

    /**
     * Check if value is "empty but meaningful"
     */
    public static function isEmpty($value): bool
    {
        return $value === null || $value === '' || $value === [];
    }

    /**
     * Convert textarea lines into associative array.
     *
     * Supported formats:
     *  - key:value
     *  - key => value
     * Ignores empty lines and comments (# or //)
     */
    public static function textareaToAssocArray($value): array
    {
        $lines = self::textareaToArray($value);
        $result = [];

        foreach ($lines as $line) {
            // Ignore comments
            if (strpos($line, '#') === 0 || strpos($line, '//') === 0) {
                continue;
            }

            // Normalize separators
            if (strpos($line, '=>') !== false) {
                [$key, $val] = explode('=>', $line, 2);
            } elseif (strpos($line, ':') !== false) {
                [$key, $val] = explode(':', $line, 2);
            } else {
                // Invalid line, skip
                continue;
            }

            $key = trim($key);
            $val = trim($val);

            if ($key === '') {
                continue;
            }

            $result[$key] = $val;
        }

        return $result;
    }

    /**
     * Ensure value is string
     */
    public static function string($value): string
    {
        if ($value === null) return '';
        if (is_string($value)) return $value;
        if (is_scalar($value)) return (string)$value;

        return json_encode($value, JSON_UNESCAPED_UNICODE) ?: '';
    }

    public static function int($value, int $default = 0): int
    {
        if ($value === null || $value === '') return $default;
        if (is_int($value)) return $value;
        if (is_bool($value)) return $value ? 1 : 0;
        if (is_numeric($value)) return (int)$value;
        return $default;
    }

    public static function float($value, float $default = 0.0): float
    {
        if ($value === null || $value === '') return $default;
        if (is_float($value)) return $value;
        if (is_int($value)) return (float)$value;
        if (is_numeric($value)) return (float)$value;
        return $default;
    }

    public static function bool($value, bool $default = false): bool
    {
        if ($value === null) return $default;
        if (is_bool($value)) return $value;

        // strings comunes de config
        $v = strtolower(trim((string)$value));
        if ($v === '1' || $v === 'true' || $v === 'yes' || $v === 'on') return true;
        if ($v === '0' || $v === 'false' || $v === 'no' || $v === 'off' || $v === '') return false;

        return $default;
    }
}