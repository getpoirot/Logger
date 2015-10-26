<?php
namespace Poirot\Logger;

use Poirot\Logger\Interfaces\iLogger;
use Psr\Log\LogLevel;

class Log
{
    /**
     * List of all loggers in the registry
     *
     * @var iLogger[]
     */
    protected static $loggers = [
        # 'Logger_name' => iLogger
    ];

    /** @var string Logger Name */
    protected static $default_error_logger;
    /** @var string Logger Name */
    protected static $default_exception_logger;

    static $ERROR_MAP = [
        E_ERROR             => LogLevel::CRITICAL,
        E_WARNING           => LogLevel::WARNING,
        E_PARSE             => LogLevel::ALERT,
        E_NOTICE            => LogLevel::NOTICE,
        E_CORE_ERROR        => LogLevel::CRITICAL,
        E_CORE_WARNING      => LogLevel::WARNING,
        E_COMPILE_ERROR     => LogLevel::ALERT,
        E_COMPILE_WARNING   => LogLevel::WARNING,
        E_USER_ERROR        => LogLevel::ERROR,
        E_USER_WARNING      => LogLevel::WARNING,
        E_USER_NOTICE       => LogLevel::NOTICE,
        E_STRICT            => LogLevel::NOTICE,
        E_RECOVERABLE_ERROR => LogLevel::ERROR,
        E_DEPRECATED        => LogLevel::NOTICE,
        E_USER_DEPRECATED   => LogLevel::NOTICE,
    ];

    /**
     * Register new logger to the registry
     *
     * @param  iLogger      $logger
     * @param  string|array $name   used to retrieve logger, array: aliases names
     *
     * @throws \Exception logger exists
     */
    static function register(iLogger $logger, $name = null)
    {
        $name = (string) $name ?: self::__attainNameFromLogger($logger);

        if (isset(self::$loggers[$name]))
            throw new \Exception('Logger with the given name already exists');

        self::$loggers[$name] = $logger;
    }

    /**
     * Gets Logger instance via static method call
     *
     * @param  string $name Logger Registered Name
     *
     * @throws \Exception
     * @return iLogger
     */
    static function __callStatic($name, $arguments)
    {
        return self::with($name);
    }

    /**
     * Gets Logger instance
     *
     * @param  string $name
     *
     * @throws \Exception Logger name not found
     * @return iLogger
     */
    static function with($name)
    {
        if (!isset(self::$loggers[(string)$name]))
            throw new \Exception(sprintf('Logger (%s) is not in the registry.', $name));

        return self::$loggers[$name];
    }

    /**
     * Removes logger from register
     *
     * @param string|iLogger $logger Name or logger instance
     */
    static function unregister($logger)
    {
        if ($logger instanceof iLogger)
            $logger = self::__attainNameFromLogger($logger);

        if (isset(self::$loggers[$logger]))
            unset(self::$loggers[$logger]);
    }

    /**
     * Has Logger Registered?
     *
     * @param string|iLogger $logger Name or logger instance
     *
     * @return bool
     */
    static function hasLogger($logger)
    {
        if ($logger instanceof iLogger)
            $logger = self::__attainNameFromLogger($logger);

        return isset(self::$loggers[$logger]);
    }


        protected static function __attainNameFromLogger(iLogger $logger)
        {
            return strtr(get_class($logger), ['\\' => '']);
        }



    // ...

    /**
     * Set Default Error Handler Logger
     *
     * - if logger instance given it must be register to
     *   registry
     *
     * - if logger is string it retrieved from registry when needed
     *
     * @param iLogger|string $logger
     */
    static function setErrorHandler($logger)
    {
        if ($logger instanceof iLogger) {
            !self::hasLogger($logger) && self::register($logger);
            $logger = self::__attainNameFromLogger($logger);
        }

        self::$default_error_logger = $logger;
    }

    /**
     * Get Default Error Handler Logger Name
     *
     * @return string Logger name
     */
    static function getErrorHandler()
    {
        if (!self::$default_error_logger)
            self::setErrorHandler('FileRotation');

        return self::$default_error_logger;
    }

    /**
     * Get Default Error Handler Logger Name
     *
     * set_error_handler(
     *    Log::getErrLogHandler()
     * )
     *
     * @return string Logger name
     */
    static function getErrLogHandler()
    {
        return function($code, $message, $file = '', $line = 0, array $context = null) {
            self::__handleError($code, $message, $file = '', $line = 0, $context = null);

            ## let error follow
            $handlers = [self::get_error_handler()];
            $static = get_class(new static);
            if (self::get_error_handler() == $static.'::getErrorHandlerCallable') {
                while ($errorHandler = restore_error_handler()) {
                    call_user_func($errorHandler, $code, $message, $file, $line, $context);
                    array_push($handlers, $errorHandler);
                }

                foreach($handlers as $eh)
                    set_error_handler($eh);
            }
        };
    }

    /**
     * Set Default Exception Handler Logger
     *
     * - if logger instance given it must be register to
     *   registry
     *
     * - if logger is string it retrieved from registry when needed
     *
     * @param iLogger|string $logger
     */
    static function setExceptionHandler($logger)
    {
        if ($logger instanceof iLogger) {
            !self::hasLogger($logger) && self::register($logger);
            $logger = self::__attainNameFromLogger($logger);
        }

        self::$default_exception_logger = $logger;
    }

    /**
     * Get Default Exception Handler Logger
     *
     * @return string Logger name
     */
    static function getExceptionHandler()
    {
        if (!self::$default_exception_logger)
            self::setExceptionHandler('FileRotation');

        return self::$default_exception_logger;
    }

    /**
     * Get Default Exception Handler Logger
     *
     * set_exception_handler(
     *    Log::getExcepLogHandler()
     * )
     *
     * @return callable
     */
    static function getExcepLogHandler()
    {
        return function($e) {
            self::__handleException($e);

            ## let exception follow
            throw $e;
        };
    }


    // private methods:

    protected static function __handleException($e)
    {
        $logger = self::with(self::getExceptionHandler());

        $logger->exception($e, ['exception' => $e]);
    }

    protected static function __handleError($code, $message, $file = '', $line = 0, array $context = null)
    {
        self::with(self::getErrorHandler())
            ->log(
                self::getLogLevelFromErrno($code)
                , $message
                , ['code' => $code, 'file' => $file, 'line' => $line]
            );
    }

    /**
     * Get Current Error Handler
     * @return callable
     */
    protected static function get_error_handler() {
        ## to get current error handler we set once and get back
        $currentErrorHandler = set_error_handler('var_dump');
        set_error_handler($currentErrorHandler);
        return $currentErrorHandler;
    }


    // Tools:

    /**
     * Get Log Level From ErrNo
     *
     * @param $errno (E_WARNING, ..)
     *
     * @return string
     */
    static function getLogLevelFromErrno($errno)
    {
        return (isset(self::$ERROR_MAP[$errno])) ? self::$ERROR_MAP[$errno] : LogLevel::ERROR;
    }

    static function codeToString($code)
    {
        switch ($code) {
            case E_ERROR:
                return 'E_ERROR';
            case E_WARNING:
                return 'E_WARNING';
            case E_PARSE:
                return 'E_PARSE';
            case E_NOTICE:
                return 'E_NOTICE';
            case E_CORE_ERROR:
                return 'E_CORE_ERROR';
            case E_CORE_WARNING:
                return 'E_CORE_WARNING';
            case E_COMPILE_ERROR:
                return 'E_COMPILE_ERROR';
            case E_COMPILE_WARNING:
                return 'E_COMPILE_WARNING';
            case E_USER_ERROR:
                return 'E_USER_ERROR';
            case E_USER_WARNING:
                return 'E_USER_WARNING';
            case E_USER_NOTICE:
                return 'E_USER_NOTICE';
            case E_STRICT:
                return 'E_STRICT';
            case E_RECOVERABLE_ERROR:
                return 'E_RECOVERABLE_ERROR';
            case E_DEPRECATED:
                return 'E_DEPRECATED';
            case E_USER_DEPRECATED:
                return 'E_USER_DEPRECATED';
        }

        return 'Unknown PHP error';
    }
}
