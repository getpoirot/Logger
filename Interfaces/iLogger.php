<?php
namespace Poirot\Logger\Interfaces;

interface iLogger extends \Psr\Log\LoggerInterface
{
    /**
     * Default Logger Context Data
     *
     * - context will merge with ::log argument context
     *
     * @return iContext
     */
    function context();

    /**
     * Prepare Before Log Message
     *
     * - callable can return false that mean
     *   don't log this message
     *
     * - context is merge context from default logger context
     *   and ::log context argument
     *
     * callable:
     * bool function($level, $message, $context)
     *
     * @param callable $callable
     *
     * @return $this
     */
    function beforeLog(callable $callable);

    /**
     * Log exception information.
     *
     * @param \Exception $exception
     *
     * @return null
     */
    function exception(\Exception $exception);

    /**
     * Filter out from the Data Context arrays before sending them
     *
     * @param array $contextData
     *
     * @return $this
     */
    function ignoreData(array $contextData);
}
