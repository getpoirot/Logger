<?php
namespace Poirot\Logger\Interfaces;

use Poirot\Std\Interfaces\Struct\iDataStruct;

/**
 * Context is DataStruct that contains (extra) data of log.
 * Usually it used on log methods context argument.
 */
interface iContext extends iDataStruct
{

}
