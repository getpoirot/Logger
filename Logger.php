<?php
namespace Poirot\Logger;

use Poirot\Logger\Interfaces\iLogger;
use Poirot\Logger\Interfaces\Logger\iSupplierLogger;
use Poirot\Logger\Logger\LogDataContext;
use Poirot\Std\Interfaces\Pact\ipConfigurable;
use Poirot\Std\Struct\CollectionObject;

/*
$logger = new Logger();
$logger->attach(new PhpLogSupplier, ['beforeSend' => function($level, $message, $context) {
    if ($level !== LogLevel::DEBUG)
        ## don`t log except of debug messages
        return false;
}]);
$logger->debug('this is debug message', ['type' => 'Debug', 'other_data' => new Entity]);
*/

class Logger extends aLogger
    implements iLogger
    , ipConfigurable
{
    /** @var CollectionObject */
    protected $__attached_suppliers;

    /**
     * Build Object With Provided Options
     *
     * @param array $options Associated Array
     * @param bool $throwException Throw Exception On Wrong Option
     *
     * @throws \Exception
     * @return $this
     */
    function with(array $options, $throwException = false)
    {
        // TODO: Implement with() method.
    }

    /**
     * Load Build Options From Given Resource
     *
     * - usually it used in cases that we have to support
     *   more than once configure situation
     *   [code:]
     *     Configurable->with(Configurable::withOf(path\to\file.conf))
     *   [code]
     *
     *
     * @param array|mixed $resource
     *
     * @throws \InvalidArgumentException if resource not supported
     * @return array
     */
    static function withOf($resource)
    {
        // TODO: Implement withOf() method.
    }

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

        /** @var iSupplierLogger $supplier */
        foreach ($this->__getObjCollection() as $supplier)
        {
            $supplierData = $this->__getObjCollection()->getData($supplier);

            try {
                if (isset($supplierData['beforeSend'])) {
                    if (call_user_func_array($supplierData['beforeSend'], [$level, $message, $context]) === false)
                        ## not allowed to log this
                        continue;
                }

                #!# LogDataContext included with default data such as Timestamp
                $supplier->send(new LogDataContext($selfContext));

            } catch (\Exception $e) { /* Let Other Logs Follow */ }
        } // end foreach
    }

    /**
     * Attach Supplier To Log
     *
     * @param iSupplierLogger $supplier
     * @param array        $data     array['beforeSend' => \Closure]
     *
     * @return $this
     */
    function attach(iSupplierLogger $supplier, array $data = [])
    {
        $this->__getObjCollection()->insert($supplier, $data);
        return $this;
    }

    /**
     * Detach Supplier
     *
     * @param iSupplierLogger $supplier
     *
     * @return $this
     */
    function detach(iSupplierLogger $supplier)
    {
        $this->__getObjCollection()->del($supplier);
        return $this;
    }

    // ...

    protected function __getObjCollection()
    {
        if (!$this->__attached_suppliers)
            $this->__attached_suppliers = new CollectionObject;

        return $this->__attached_suppliers;
    }
}
