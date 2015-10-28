<?php
namespace Poirot\Logger\Logger;

use Poirot\Core\Interfaces\iDataSetConveyor;

class NullSupplier extends AbstractSupplier
{
    protected function doSend(iDataSetConveyor $logData)
    { }
}
