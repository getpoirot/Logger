<?php
namespace Poirot\Logger\Logger;

use Poirot\Logger\Interfaces\Logger\iLogData;
use Poirot\Std\Struct\DataOptionsOpen;

class LogDataContext extends DataOptionsOpen
    implements iLogData
{
    protected $level;
    protected $message;

    protected $timestamp;

    /**
     * Set Level
     *
     * @param int $level
     *
     * @return $this
     */
    function setLevel($level)
    {
        $this->level = $level;
        return $this;
    }

    /**
     * Get Level
     *
     * @return int
     */
    function getLevel()
    {
        return $this->level;
    }

    /**
     * Set Message Log
     *
     * @param string $message
     *
     * @return $this
     */
    function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Get Message
     *
     * @return string
     */
    function getMessage()
    {
        return $this->message;
    }


    // ...

    /**
     * @return mixed
     */
    function getTimestamp()
    {
        if (!$this->timestamp)
            $this->timestamp = new \DateTime();

        return $this->timestamp;
    }
}
