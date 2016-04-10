<?php
namespace Poirot\Logger;

use Psr\Log\LogLevel;

use Poirot\Logger\Interfaces\iLogger;
use Poirot\Logger\Logger\Context\ContextAggregate;

abstract class aLogger
    extends    \Psr\Log\AbstractLogger
    implements iLogger
{
    /** @var ContextAggregate */
    protected $context;

    /**
     * Logs with an arbitrary level.
     *
     * - context will merge with default logger context
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    abstract function log($level, $message, array $context = []);


    /**
     * Default Logger Context Data
     *
     * - default context will merge with self::log method argument context
     *
     * @return ContextAggregate
     */
    function context()
    {
        if (!$this->context)
            $this->context = new ContextAggregate;

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
}
