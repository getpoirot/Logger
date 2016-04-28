<?php
namespace Poirot\Logger\Interfaces;

use Psr\Log\LoggerInterface;

interface iLogger 
    extends LoggerInterface
{
    /**
     * Default Logger Context Data
     *
     * - default context will merge with self::log method argument context
     *
     * @return iContext
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
    function exception(\Exception $exception, array $context = array());
}
