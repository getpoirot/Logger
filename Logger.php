<?php
namespace Poirot\Logger;

use Poirot\Logger\Interfaces\iLogger;
use Poirot\Logger\Logger\AbstractLogger;
use Poirot\Logger\Logger\LogData;

class Logger extends AbstractLogger
    implements iLogger
{
    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    function log($level, $message, array $context = [])
    {
        $context['level']   = $level;
        $context['message'] = $message;

        $selfContext = clone $this->context();
        $selfContext->from($context)->toArray(); ## merge with default context

        foreach (clone $this->writers as $supplier)
            $supplier->send(new LogData($selfContext));
    }
}
