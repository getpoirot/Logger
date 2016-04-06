<?php
namespace Poirot\Logger;

use Poirot\Logger\Context\AggregateContext;
use Poirot\Logger\Interfaces\iLogger;
use Psr\Log\LogLevel;

abstract class AbstractLogger extends \Psr\Log\AbstractLogger
    implements iLogger
{
    /** @var AggregateContext */
    protected $context;
    /** @var array Ignore Context Data From Log */
    protected $_ignore_data = [];
    /**
     * @var callable It trigger just before log
     * bool function($level, $message, $context)
     */
    protected $_beforeLog;

    /**
     * Default Logger Context Data
     *
     * - default context will merge with self::log method argument context
     *
     * @return AggregateContext
     */
    function context()
    {
        if (!$this->context)
            $this->context = new AggregateContext;

        return $this->context;
    }

    /**
     * Log exception information.
     *
     * @param \Exception $exception
     * @param array      $context
     *
     * @return null
     */
    function exception(\Exception $exception, array $context = [])
    {
        $uid   = spl_object_hash($exception);
        $extra = [
            'uid'   => $uid,
            'file'  => $exception->getFile(),
            'line'  => $exception->getLine(),
            'trace' => $exception->getTrace(),
        ];

        if (isset($exception->xdebug_message))
            $extra['xdebug'] = $exception->xdebug_message;

        $this->log(LogLevel::CRITICAL, $exception->getMessage(), array_merge($context, $extra));
        if (isset($context['belong']))
            ## avoid storing previous exceptions again
            return;

        do {
            $exception = $exception->getPrevious();
            if ($exception)
                ## pass belong context
                $this->exception($exception, ['belong' => $uid]);
        } while ($exception);
    }

    /**
     * Prepare Before Log Message
     *
     * - callable can return false that mean
     *   don't log this message
     *
     * - context will merge with default logger context
     *
     * callable:
     * bool function($level, $message, $context)
     *
     * @param callable $callable
     *
     * @return $this
     */
    // abstract function beforeLog(callable $callable);

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    abstract function log($level, $message, array $context = []);
}
