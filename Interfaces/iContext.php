<?php
namespace Poirot\Logger\Interfaces;

use Poirot\Core\Interfaces\iPoirotOptions;

interface iContext extends iPoirotOptions
{
    /**
     * Get Context Name
     *
     * @return string
     */
    function getName();
}
