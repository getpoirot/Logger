<?php
namespace Poirot\Logger\Interfaces\Logger;

use Poirot\Core\Interfaces\iPoirotOptions;

interface iLogData extends iPoirotOptions
{
    /**
     * Set Level
     *
     * @param int $level
     *
     * @return $this
     */
    function setLevel($level);

    /**
     * Get Level
     *
     * @return int
     */
    function getLevel();

    /**
     * Set Message Log
     *
     * @param string $message
     *
     * @return $this
     */
    function setMessage($message);

    /**
     * Get Message
     *
     * @return string
     */
    function getMessage();


    // Other options consumed as extra data set
    // ...
}
