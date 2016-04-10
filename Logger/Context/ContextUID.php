<?php
namespace Poirot\Logger\Logger\Context;

class ContextUID
    extends aContext
{
    /**
     * unique identifier
     *
     * @var string
     */
    protected $uid;

    /**
     * Get Current Process Identifier
     *
     * @return string
     */
    function getUid()
    {
        if ($this->uid)
            return $this->uid;

        $requestTime = (version_compare(PHP_VERSION, '5.4.0') >= 0)
            ? $_SERVER['REQUEST_TIME_FLOAT']
            : $_SERVER['REQUEST_TIME'];

        if (PHP_SAPI == 'cli')
            $this->uid = md5($requestTime);
        elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $this->uid = md5($requestTime . $_SERVER['HTTP_X_FORWARDED_FOR']);
            return $this->uid;
        } else
            $this->uid = md5($requestTime . $_SERVER['REMOTE_ADDR']);

        return $this->uid;
    }
}
