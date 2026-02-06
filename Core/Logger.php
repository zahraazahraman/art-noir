<?php
/**
 * Logger Class
 * 
 * Provides logging functionality for the ArtNoir application.
 * Logs actions to a .log file with configurable log levels.
 * 
 * Log Format: [TIMESTAMP] [LEVEL] MESSAGE | Context: key=value, key=value
 * 
 * @author ArtNoir Development Team
 * @version 1.0.0
 */

class Logger
{
    /**
     * Log levels
     */
    const LEVEL_INFO = 'INFO';
    const LEVEL_ERROR = 'ERROR';
    const LEVEL_WARNING = 'WARNING';
    const LEVEL_DEBUG = 'DEBUG';

    /**
     * Log file path
     * @var string
     */
    private static $logFile = __DIR__ . '/../logs/app.log';

    /**
     * Enable/disable logging
     * @var bool
     */
    private static $enabled = true;

    /**
     * Minimum log level to write
     * @var string
     */
    private static $minLevel = self::LEVEL_DEBUG;

    /**
     * Log file handle
     * @var resource|null
     */
    private static $fileHandle = null;

    /**
     * Initialize the logger
     * 
     * @param string $logFilePath Optional custom log file path
     * @param string $minLevel Minimum log level to record
     * @param bool $enabled Enable or disable logging
     */
    public static function init(string $logFilePath = null, string $minLevel = self::LEVEL_DEBUG, bool $enabled = true): void
    {
        if ($logFilePath !== null) {
            self::$logFile = $logFilePath;
        }
        
        self::$minLevel = $minLevel;
        self::$enabled = $enabled;

        // Ensure log directory exists
        self::ensureLogDirectory();
    }

    /**
     * Get the current log file path
     * 
     * @return string
     */
    public static function getLogFile(): string
    {
        return self::$logFile;
    }

    /**
     * Set the log file path
     * 
     * @param string $path New log file path
     */
    public static function setLogFile(string $path): void
    {
        self::$logFile = $path;
        self::ensureLogDirectory();
    }

    /**
     * Enable or disable logging
     * 
     * @param bool $enabled
     */
    public static function setEnabled(bool $enabled): void
    {
        self::$enabled = $enabled;
    }

    /**
     * Check if logging is enabled
     * 
     * @return bool
     */
    public static function isEnabled(): bool
    {
        return self::$enabled;
    }

    /**
     * Set minimum log level
     * 
     * @param string $level Log level (INFO, ERROR, WARNING, DEBUG)
     */
    public static function setMinLevel(string $level): void
    {
        self::$minLevel = $level;
    }

    /**
     * Get current minimum log level
     * 
     * @return string
     */
    public static function getMinLevel(): string
    {
        return self::$minLevel;
    }

    /**
     * Log an info message
     * 
     * @param string $message The log message
     * @param array $context Additional context data
     */
    public static function info(string $message, array $context = []): void
    {
        self::log(self::LEVEL_INFO, $message, $context);
    }

    /**
     * Log an error message
     * 
     * @param string $message The log message
     * @param array $context Additional context data
     */
    public static function error(string $message, array $context = []): void
    {
        self::log(self::LEVEL_ERROR, $message, $context);
    }

    /**
     * Log a warning message
     * 
     * @param string $message The log message
     * @param array $context Additional context data
     */
    public static function warning(string $message, array $context = []): void
    {
        self::log(self::LEVEL_WARNING, $message, $context);
    }

    /**
     * Log a debug message
     * 
     * @param string $message The log message
     * @param array $context Additional context data
     */
    public static function debug(string $message, array $context = []): void
    {
        self::log(self::LEVEL_DEBUG, $message, $context);
    }

    /**
     * Log a custom message with specified level
     * 
     * @param string $level Log level
     * @param string $message The log message
     * @param array $context Additional context data
     */
    public static function log(string $level, string $message, array $context = []): void
    {
        // Check if logging is enabled
        if (!self::$enabled) {
            return;
        }

        // Check if level meets minimum threshold
        if (!self::shouldLog($level)) {
            return;
        }

        // Ensure log directory exists
        self::ensureLogDirectory();

        // Format the log entry
        $timestamp = self::getTimestamp();
        $contextString = self::formatContext($context);
        
        $logEntry = "[{$timestamp}] [{$level}] {$message}";
        if (!empty($contextString)) {
            $logEntry .= " | Context: {$contextString}";
        }
        $logEntry .= PHP_EOL;

        // Write to log file
        self::writeToFile($logEntry);
    }

    /**
     * Log an exception/error with full stack trace
     * 
     * @param \Throwable $exception The exception to log
     * @param string $message Optional custom message
     * @param array $context Additional context data
     */
    public static function exception(\Throwable $exception, string $message = '', array $context = []): void
    {
        $logMessage = !empty($message) ? $message : 'Exception occurred';
        
        $context = array_merge($context, [
            'exception_class' => get_class($exception),
            'exception_message' => $exception->getMessage(),
            'exception_file' => $exception->getFile(),
            'exception_line' => $exception->getLine(),
            'stack_trace' => $exception->getTraceAsString()
        ]);

        self::log(self::LEVEL_ERROR, $logMessage, $context);
    }

    /**
     * Log user authentication events
     * 
     * @param string $action Action type (login, logout, register, failed_login)
     * @param array $userData User information
     * @param bool $success Whether the action was successful
     * @param string $message Optional custom message
     */
    public static function auth(string $action, array $userData, bool $success, string $message = ''): void
    {
        $level = $success ? self::LEVEL_INFO : self::LEVEL_WARNING;
        
        $context = array_merge([
            'action' => $action,
            'success' => $success,
            'user_id' => $userData['id'] ?? null,
            'email' => $userData['email'] ?? null,
            'role' => $userData['role'] ?? null,
            'ip_address' => self::getClientIP()
        ], $userData);

        $logMessage = $message ?: "User {$action} - " . ($success ? 'Success' : 'Failed');

        self::log($level, $logMessage, $context);
    }

    /**
     * Log database operations
     * 
     * @param string $operation Operation type (INSERT, UPDATE, DELETE, SELECT)
     * @param string $table Database table name
     * @param array $data Operation data
     * @param bool $success Whether the operation was successful
     */
    public static function database(string $operation, string $table, array $data, bool $success): void
    {
        $level = $success ? self::LEVEL_INFO : self::LEVEL_ERROR;
        
        $context = [
            'operation' => $operation,
            'table' => $table,
            'success' => $success,
            'data' => json_encode($data)
        ];

        $logMessage = "Database {$operation} on table '{$table}' - " . ($success ? 'Success' : 'Failed');

        self::log($level, $logMessage, $context);
    }

    /**
     * Log API/WebService requests
     * 
     * @param string $endpoint API endpoint
     * @param string $method HTTP method
     * @param array $params Request parameters
     * @param int $responseCode HTTP response code
     * @param float $duration Request duration in seconds
     */
    public static function api(string $endpoint, string $method, array $params, int $responseCode, float $duration): void
    {
        $level = $responseCode >= 400 ? self::LEVEL_WARNING : self::LEVEL_INFO;
        
        $context = [
            'endpoint' => $endpoint,
            'method' => $method,
            'response_code' => $responseCode,
            'duration_ms' => round($duration * 1000, 2),
            'params' => json_encode($params),
            'ip_address' => self::getClientIP()
        ];

        $logMessage = "API Request: {$method} {$endpoint} - Response: {$responseCode} ({$duration}s)";

        self::log($level, $logMessage, $context);
    }

    /**
     * Read log entries from the log file
     * 
     * @param int $lines Number of lines to read (default: 100)
     * @param string $level Optional filter by log level
     * @return array Array of log entries
     */
    public static function read(int $lines = 100, string $level = null): array
    {
        if (!file_exists(self::$logFile)) {
            return [];
        }

        $logEntries = [];
        $file = new SplFileObject(self::$logFile, 'r');
        
        // Seek to end and work backwards
        $file->seek(PHP_INT_MAX);
        $totalLines = $file->key();
        
        $startLine = max(0, $totalLines - $lines);
        $file->seek($startLine);
        
        while (!$file->eof()) {
            $line = $file->fgets();
            if (!empty(trim($line))) {
                // Filter by level if specified
                if ($level !== null && strpos($line, "[{$level}]") === false) {
                    continue;
                }
                $logEntries[] = trim($line);
            }
        }

        return $logEntries;
    }

    /**
     * Clear the log file
     */
    public static function clear(): void
    {
        if (file_exists(self::$logFile)) {
            file_put_contents(self::$logFile, '');
        }
    }

    /**
     * Get log file size in bytes
     * 
     * @return int
     */
    public static function getSize(): int
    {
        if (!file_exists(self::$logFile)) {
            return 0;
        }
        return filesize(self::$logFile);
    }

    /**
     * Get formatted log file size
     * 
     * @return string
     */
    public static function getFormattedSize(): string
    {
        $size = self::getSize();
        
        if ($size >= 1048576) {
            return round($size / 1048576, 2) . ' MB';
        } elseif ($size >= 1024) {
            return round($size / 1024, 2) . ' KB';
        }
        
        return $size . ' bytes';
    }

    /**
     * Ensure log directory exists
     */
    private static function ensureLogDirectory(): void
    {
        $logDir = dirname(self::$logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }

    /**
     * Get formatted timestamp
     * 
     * @return string
     */
    private static function getTimestamp(): string
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * Get client IP address
     * 
     * @return string
     */
    private static function getClientIP(): string
    {
        $ipKeys = [
            'HTTP_CF_CONNECTING_IP',     // Cloudflare
            'HTTP_X_FORWARDED_FOR',      // Proxy
            'HTTP_X_REAL_IP',            // Nginx proxy
            'HTTP_CLIENT_IP',            // Client IP
            'REMOTE_ADDR'                // Standard
        ];

        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) && !empty($_SERVER[$key])) {
                $ip = explode(',', $_SERVER[$key])[0];
                return trim($ip);
            }
        }

        return '0.0.0.0';
    }

    /**
     * Format context array as string
     * 
     * @param array $context
     * @return string
     */
    private static function formatContext(array $context): string
    {
        if (empty($context)) {
            return '';
        }

        $parts = [];
        foreach ($context as $key => $value) {
            if (is_array($value) || is_object($value)) {
                $value = json_encode($value);
            } elseif (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            } elseif (is_null($value)) {
                $value = 'null';
            }
            
            // Sanitize the value
            $value = str_replace(['|', ',', ']'], '', (string)$value);
            $parts[] = "{$key}={$value}";
        }

        return implode(', ', $parts);
    }

    /**
     * Check if the given level should be logged based on minimum level
     * 
     * @param string $level
     * @return bool
     */
    private static function shouldLog(string $level): bool
    {
        $levels = [
            self::LEVEL_DEBUG => 0,
            self::LEVEL_INFO => 1,
            self::LEVEL_WARNING => 2,
            self::LEVEL_ERROR => 3
        ];

        $currentLevel = $levels[$level] ?? 0;
        $minLevel = $levels[self::$minLevel] ?? 0;

        return $currentLevel >= $minLevel;
    }

    /**
     * Write to log file
     * 
     * @param string $content
     */
    private static function writeToFile(string $content): void
    {
        try {
            // Use file_put_contents with FILE_APPEND for thread safety
            $result = file_put_contents(self::$logFile, $content, FILE_APPEND | LOCK_EX);
            
            if ($result === false) {
                // Fallback to error_log if file write fails
                error_log("Logger: Failed to write to log file - " . self::$logFile);
            }
        } catch (\Exception $e) {
            // Catch any exceptions and log to PHP error log
            error_log("Logger Exception: " . $e->getMessage());
        }
    }

    /**
     * Destructor - close file handle if open
     */
    public function __destruct()
    {
        if (self::$fileHandle !== null) {
            fclose(self::$fileHandle);
            self::$fileHandle = null;
        }
    }
}

// Initialize logger with default settings
Logger::init();

