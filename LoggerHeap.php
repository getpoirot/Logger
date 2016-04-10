<?php
namespace Poirot\Logger;

use Poirot\Std\ErrorStack;
use Poirot\Std\Interfaces\Pact\ipConfigurable;
use Poirot\Std\Struct\CollectionObject;
use Poirot\Logger\Interfaces\iLogger;
use Poirot\Logger\Logger\ContextDefault;
use Poirot\Logger\LoggerHeap\Interfaces\iHeapLogger;

/*
$logger = new LoggerHeap();
$logger->attach(new PhpLogSupplier, ['_beforeSend' => function($level, $message, iContext $context) {
    if ($level !== LogLevel::DEBUG)
        ## don`t log except of debug messages
        return false;
}]);
$logger->debug('this is debug message', ['type' => 'Debug', 'other_data' => new Entity]);
*/

class LoggerHeap
    extends    aLogger
    implements iLogger
    , ipConfigurable
{
    /** @var CollectionObject */
    protected $_attached_heaps;


    /**
     * LoggerHeap constructor.
     * @param null|array $options
     */
    function __construct($options = null)
    {
        if ($options !== null)
            $this->with($options);
    }

    /**
     * Build Object With Provided Options
     * Options:[
     *  'attach'  => [ iLogger, 'Logger\ClassName', 'Logger\ClassName' => ['_def_context'=>[], logOptions..] ]
     *               iLogger |  'Logger\ClassName'
     *  'context' => iContext | [contextOptions..]
     *
     * @param array $options        Associated Array
     * @param bool  $throwException Throw Exception On Wrong Option
     *
     * @throws \Exception
     * @return $this
     */
    function with(array $options, $throwException = false)
    {
        if ($throwException && !(isset($options['attach']) || isset($options['context'])))
            throw new \InvalidArgumentException('Invalid Options Provided.');

        if (isset($options['attach']) && $attach = $options['attach']) {
            if ($attach instanceof iLogger || is_string($attach))
                $attach = [$attach];

            foreach($attach as $p => $b)
            {
                /** @var iHeapLogger $heapLogger */
                if (is_string($p)) {
                    // 'Logger\ClassName' => [logOptions..]
                    $heapLogger  = $p;
                    $options     = $b;
                } else {
                    // [iLogger, ]
                    $heapLogger  = $b;
                    $options     = null;
                }

                $defContext = [];
                if (is_array($options) && isset($options['_def_context']))
                    ## default context data attached to heap log
                    $defContext = $options['_def_context'];

                // ['Logger\ClassName', ]
                if (is_string($heapLogger) && is_int($p)) {
                    (class_exists($heapLogger)) && $heapLogger = new $heapLogger($options);
                }

                $this->attach($heapLogger, $defContext);
            }
        }

        if (isset($options['context']) && $context = $options['context'])
            $this->context()->from($context);

        return $this;
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
     * @param array|mixed $optionsResource
     *
     * @throws \InvalidArgumentException if resource not supported
     * @return array
     */
    static function withOf($optionsResource)
    {
        if (!is_array($optionsResource))
            throw new \InvalidArgumentException(sprintf(
                'Options as Resource Just Support Array, given: (%s).'
                , \Poirot\Std\flatten($optionsResource)
            ));

        return $optionsResource;
    }

    /**
     * Logs with an arbitrary level.
     *
     * - merge given context with default context data clone
     * - merge heap context with context data follow
     * - from attached heaps call "_beforeSend" data callable.
     *   if false return skip log for this heap
     * - write context into log through heap
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    function log($level, $message, array $context = [])
    {
        $selfContext = clone $this->context();
        $selfContext->from($context); ## merge with default context

        /** @var iHeapLogger $heapSupplier */
        foreach ($this->__getObjCollection() as $heapSupplier)
        {
            $heapAttachedContext = $this->__getObjCollection()->getData($heapSupplier);
            if (isset($heapAttachedContext['_beforeSend'])) {
                $callable = $heapAttachedContext['_beforeSend'];
                unset($heapAttachedContext['_beforeSend']);
            }

            $context = new ContextDefault($selfContext); // #!# Context included with default data such as Timestamp
            ## set attached heap specific context
            ## it will overwrite defaults
            $context->from($heapAttachedContext);

            // ..

            ErrorStack::handleException(function($e) {/* Let Other Logs Follow */});

            if (isset($callable) && false === call_user_func($callable, $level, $message, $context))
                ## not allowed to log this
                continue;

            $context->from(['level' => $level, 'message' => $message]);
            $heapSupplier->write($context);

            ErrorStack::handleDone();

        } // end foreach
    }

    /**
     * Attach Heap To Log
     *
     * @param iHeapLogger $supplier
     * @param array        $data     array['_beforeSend' => \Closure]
     *
     * @return $this
     */
    function attach(iHeapLogger $supplier, array $data = [])
    {
        $this->__getObjCollection()->insert($supplier, $data);
        return $this;
    }

    /**
     * Detach Heap
     *
     * @param iHeapLogger $supplier
     *
     * @return $this
     */
    function detach(iHeapLogger $supplier)
    {
        $this->__getObjCollection()->del($supplier);
        return $this;
    }

    // ...

    protected function __getObjCollection()
    {
        if (!$this->_attached_heaps)
            $this->_attached_heaps = new CollectionObject;

        return $this->_attached_heaps;
    }
}
