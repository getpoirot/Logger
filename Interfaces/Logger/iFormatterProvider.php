<?php
namespace Poirot\Logger\Interfaces\Logger;

use Poirot\Logger\Interfaces\iFormatter;

interface iFormatterProvider
{
    /**
     * Get Formatter
     *
     * @return iFormatter
     */
    function formatter();
}
