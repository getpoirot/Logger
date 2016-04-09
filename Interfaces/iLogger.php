<?php
namespace Poirot\Logger\Interfaces;

use Poirot\Logger\Context\ContextAggregate;

interface iLogger extends \Psr\Log\LoggerInterface
{
    /**
     * Default Logger Context Data
     *
     * - default context will merge with self::log method argument context
     *
     * @return ContextAggregate
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
