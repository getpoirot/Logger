<?php
namespace Poirot\Logger\Interfaces;

use Poirot\Logger\Context\AggregateContext;

interface iLogger extends \Psr\Log\LoggerInterface
{
    /**
     * Default Logger Context Data
     *
     * - default context will merge with self::log method argument context
     *
     * @return AggregateContext
     */
    function context();

    /**
     * Log exception information.
     *
     * @param \Exception $exception
     * @param array      $context
     *
     * @return null
     */
    function exception(\Exception $exception, array $context = []);
}
