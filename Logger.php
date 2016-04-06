<?php
namespace Poirot\Logger;

use Poirot\Logger\Interfaces\iLogger;
use Poirot\Logger\Interfaces\Logger\iLogSupplier;
use Poirot\Std\Struct\ObjectCollection;

/*
$logger = new Logger();
$logger->attach(new PhpLogSupplier, ['beforeSend' => function($level, $message, $context) {
    if ($level !== LogLevel::DEBUG)
        ## don`t log except of debug messages
        return false;
}]);
$logger->debug('this is debug message', ['type' => 'Debug', 'other_data' => new Entity]);
*/

class Logger extends AbstractLogger
    implements iLogger
{
    /** @var ObjectCollection */
    protected $__attached_suppliers;

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
        $selfContext->from($context); ## merge with default context

        /** @var iLogSupplier $supplier */
        foreach ($this->__getObjCollection() as $supplier)
        {
            $supplierData = $this->__getObjCollection()->getData($supplier);

            try {
                if (isset($supplierData['beforeSend'])) {
                    if (call_user_func_array($supplierData['beforeSend'], [$level, $message, $context]) === false)
                        ## not allowed to log this
                        continue;
                }

                #!# LogDataContext include default data like Timestamp
                $supplier->send(new LogDataContext($selfContext));

            } catch (\Exception $e) { /* Let Other Logs Follow */ }
        } // end foreach
    }

    /**
     * Attach Supplier To Log
     *
     * @param iLogSupplier $supplier
     * @param array        $data     array['beforeSend' => \Closure]
     *
     * @return $this
     */
    function attach(iLogSupplier $supplier, array $data = [])
    {
        $this->__getObjCollection()->insert($supplier, $data);
        return $this;
    }

    /**
     * Detach Supplier
     *
     * @param iLogSupplier $supplier
     *
     * @return $this
     */
    function detach(iLogSupplier $supplier)
    {
        $this->__getObjCollection()->del($supplier);
        return $this;
    }

    // ...

    protected function __getObjCollection()
    {
        if (!$this->__attached_suppliers)
            $this->__attached_suppliers = new ObjectCollection;

        return $this->__attached_suppliers;
    }
}
