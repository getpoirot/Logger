<?php
namespace Poirot\Logger\Supplier;

use Poirot\Core\Interfaces\iDataSetConveyor;

class NullSupplier extends AbstractSupplier
{
    protected function doSend(iDataSetConveyor $logData)
    { }
}
