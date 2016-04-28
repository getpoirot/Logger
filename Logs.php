<?php
namespace Poirot\Logger;

use Poirot\Logger\Interfaces\iLogger;
use Psr\Log\LogLevel as PsrLogLevel;

class Logs
{
    /**
     * List of all loggers in the registry
     *
     * @var iLogger[]
     */
    protected static $loggers = array(
        # 'Logger\Name' => iLogger
        # 'alias_name'  => 'Logger\Name'
    );

    /** @var string Logger Name */
    protected static $default_error_logger;
    /** @var string Logger Name */
    protected static $default_exception_logger;

    static $ERROR_MAP = array(
        E_ERROR             => PsrLogLevel::CRITICAL,
        E_WARNING           => PsrLogLevel::WARNING,
        E_PARSE             => PsrLogLevel::ALERT,
        E_NOTICE            => PsrLogLevel::NOTICE,
        E_CORE_ERROR        => PsrLogLevel::CRITICAL,
        E_CORE_WARNING      => PsrLogLevel::WARNING,
        E_COMPILE_ERROR     => PsrLogLevel::ALERT,
        E_COMPILE_WARNING   => PsrLogLevel::WARNING,
        E_USER_ERROR        => PsrLogLevel::ERROR,
        E_USER_WARNING      => PsrLogLevel::WARNING,
        E_USER_NOTICE       => PsrLogLevel::NOTICE,
        E_STRICT            => PsrLogLevel::NOTICE,
        E_RECOVERABLE_ERROR => PsrLogLevel::ERROR,
        E_DEPRECATED        => PsrLogLevel::NOTICE,
        E_USER_DEPRECATED   => PsrLogLevel::NOTICE,
    );

    /**
     * Build Object With Provided Options
     *
     * Options: [
     *   'register'     => [iLogger, 'name' => iLogger|'To\ClassName']
     *   'register'     => ['name' => iLogger|'To\ClassName', 'name' => [iLogger|'To\ClassName', 'alias', $alias2, ..] ]
     *   'alias_name'   => ['nameOrAlias' => alias | ['alias', 'alias2', ..] ]
     *
     *   'error_logger'     => nameAlias | iLogger
     *   'exception_logger' => nameAlias | iLogger
     *
     * @param array $options        Associated Array
     * @param bool  $throwException Throw Exception On Wrong Option
     *
     * @throws \Exception
     */
    static function with(array $options, $throwException = false)
    {
        if ($throwException && !(isset($options['register']) || isset($options['alias_name'])))
            throw new \InvalidArgumentException('Invalid Option Provided.');

        if (isset($options['register']) && $register = $options['register']) {
            foreach ($register as $name => $instanceAliases) {
                if (is_string($instanceAliases))
                    // 'name' => 'To\ClassName' | 'To\ClassName',
                    $instanceAliases = new $instanceAliases;

                if (is_int($name))
                    // [ iLogger, 'To\ClassName' ]
                    $name = null;

                if (!is_array($instanceAliases))
                    ## ['name' => EnvBase|'To\ClassName'
                    $instanceAliases = array($instanceAliases);

                $logger = array_shift($instanceAliases);
                // remaining items is aliases
                self::register($logger, $name, $instanceAliases);
            }
        }


        if (isset($options['alias_name']) && $aliases = $options['alias_name']) {
            foreach ($aliases as $nameOrAlias => $alias) {
                if (!is_array($alias))
                    ## ['name' => EnvBase|'To\ClassName'
                    $alias = array($alias);

                self::setAlias($nameOrAlias, $alias);
            }
        }


        if (isset($options['error_logger']) && $logger = $options['error_logger'])
            self::setErrorLogger($logger);


        if (isset($options['exception_logger']) && $logger = $options['exception_logger'])
            self::setExceptionLogger($logger);
    }

    /**
     * Register new logger to the registry
     *
     * ! in the case that no name provided logger class name
     *   will used.
     *   register(new LoggerHeap, Poirot\Logger\LoggerHeap::class)
     *   register(new LoggerHeap, 'NotifyDeveloper')
     *
     * @param iLogger      $logger
     * @param string|array $name     Used to retrieve logger
     * @param array        $aliases  Name Aliases
     *
     * @throws \Exception logger exists
     */
    static function register(iLogger $logger, $name = null, array $aliases = array())
    {
        $name = (string) $name ?: static::_attainNameFromLogger($logger);

        if (isset(self::$loggers[$name]))
            throw new \Exception(sprintf(
                'Logger with the given name or alias (%s) already exists.'
                , $name
            ));

        self::$loggers[$name] = $logger;
        self::setAlias($name, $aliases);
    }

    /**
     * Set Alias Or Name Aliases
     *
     * @param string       $name  Alias Or Name
     * @param array|string $alias Alias(es)
     */
    static function setAlias($name, $alias)
    {
        if (!is_array($alias))
            $alias = array($alias);

        foreach($alias as $a)
            self::$loggers[(string) $a] = (string) $name;
    }

    /**
     * Gets Logger instance
     *
     * [code:]
     *   by(Poirot\Logger\LoggerHeap::class)
     *   by('NotifyDeveloper')
     * [code]
     *
     * @param  string $name
     *
     * @throws \Exception Logger name not found
     * @return iLogger
     */
    static function by($name)
    {
        ## find alias names
        $alias = $name;
        while(isset(self::$loggers[$alias])) {
            $alias = self::$loggers[$alias];
            if ($alias instanceof iLogger)
                return $alias;
        }

        throw new \Exception(sprintf('Logger (%s) is not in the registry.', $name));
    }

    /**
     * Gets Logger instance via static method call
     *
     * [code:]
     *  iLogger Log::MyCritical()
     * [code]
     *
     * @param  string $name Logger Registered Name
     *
     * @throws \Exception
     * @return iLogger
     */
    static function __callStatic($name, $arguments)
    {
        return self::by($name);
    }

    /**
     * Removes logger from register
     *
     * @param string|iLogger $logger Name or logger instance
     */
    static function unregister($logger)
    {
        ## find alias names
        if (is_string($logger)) {
            $alias = $logger;
            while(isset(self::$loggers[$alias])) {
                $alias = self::$loggers[$alias];
                if ($alias instanceof iLogger) {
                    $logger = $alias;
                    break;
                }
            }
        }

        if ($logger instanceof iLogger)
            $logger = self::_attainNameFromLogger($logger);

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
        ## find alias names
        if (is_string($logger)) {
            $alias = $logger;
            while(isset(self::$loggers[$alias])) {
                $alias = self::$loggers[$alias];
                if ($alias instanceof iLogger)
                    return true;
            }
        }

        if ($logger instanceof iLogger) {
            $logger = self::_attainNameFromLogger($logger);
            return isset(self::$loggers[$logger]);
        }

        return false;
    }


    // ...

    /**
     * Set Default Error Handler Logger
     *
     * - if logger instance given it will be register
     * - if logger is string it retrieved from registry when needed
     *
     * @param iLogger|string $logger
     */
    static function setErrorLogger($logger)
    {
        if ($logger instanceof iLogger) {
            !  self::hasLogger($logger)
            && self::register($logger);

            $logger = self::_attainNameFromLogger($logger);
        }

        self::$default_error_logger = $logger;
    }

    /**
     * Get Default Error Handler Logger Name
     *
     * @return string|null Logger name
     */
    static function getErrorLogger()
    {
        return self::$default_error_logger;
    }

    /**
     * Get Default Error Handler Logger Callable
     * That Can Be Set As Below:
     *
     * [code:]
     *  set_error_handler(
     *        Log::getErrLogHandler()
     *  )
     * [code]
     *
     * @return callable
     */
    static function getErrLogHandler()
    {
        return function($code, $message, $file = '', $line = 0) {
            self::_handleError($code, $message, $file = '', $line = 0);

            ## let error follow
            $handlers = array(self::get_error_handler());
            $static = get_class(new static);
            if (self::get_error_handler() == $static.'::getErrLogHandler') {
                while ($errorHandler = restore_error_handler()) {
                    call_user_func($errorHandler, $code, $message, $file, $line);
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
     * - if logger instance given it will be register
     * - if logger is string it retrieved from registry when needed
     *
     * @param iLogger|string $logger
     */
    static function setExceptionLogger($logger)
    {
        if ($logger instanceof iLogger) {
            !self::hasLogger($logger) && self::register($logger);
            $logger = self::_attainNameFromLogger($logger);
        }

        self::$default_exception_logger = $logger;
    }

    /**
     * Get Default Exception Handler Logger
     *
     * @return string Logger name
     */
    static function getExceptionLogger()
    {
        return self::$default_exception_logger;
    }

    /**
     * Get Default Exception Handler Logger Callable
     * That Can Be Set As Below:
     *
     * [code:]
     *  set_exception_handler(
     *      Log::getExcepLogHandler()
     *  )
     * [code]
     *
     * @return callable
     */
    static function getExceptionLogHandler()
    {
        return function($e) {
            self::_handleException($e);

            ## let exception follow
            throw $e;
        };
    }


    // private methods:

    protected static function _attainNameFromLogger(iLogger $logger)
    {
        return get_class($logger);
    }


    protected static function _handleException($e)
    {
        self::by(self::getExceptionLogger())
            ->exception($e)
        ;
    }

    protected static function _handleError($code, $message, $file = '', $line = 0)
    {
        self::by(self::getErrorLogger())
            ->log(
                self::getLogLevelFromErrno($code)
                , $message
                , array('code' => $code, 'file' => $file, 'line' => $line)
            )
        ;
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
        return (isset(self::$ERROR_MAP[$errno])) ? self::$ERROR_MAP[$errno] : PsrLogLevel::ERROR;
    }
}
