<?php
namespace Poirot\Logger\Formatter;

use Poirot\Core\Interfaces\iDataSetConveyor;
use Poirot\Logger\Interfaces\Logger\iLogData;

/**
 * Processes a record's message according to PSR-3 rules
 *
 * It replaces {foo} with the value from $this->getFoo()
 */
class PsrLogMessageFormatter extends AbstractFormatter
{
    // {} will replace by all extra data
    const DEFAULT_TEMPLATE = '{timestamp} ({level}): {message} {}';

    protected $template;

    /**
     * Format Data To String
     *
     * @param iLogData $logData
     *
     * @return string
     */
    function format(iLogData $logData)
    {
        $template = $this->getTemplate();

        if (false === strpos($template, '{'))
            ## nothing can be replaced, so return template self
            return $template;

        $replacements = [];
        foreach ($logData->props()->readable as $key) {
            $val = $logData->__get($key);
            $replacements['{'.$key.'}'] = $this->flatten($val);

            // got extra values
        }

        $return = strtr($template, $replacements);
        return $return;
    }


    // ..

    /**
     * @return string
     */
    function getTemplate()
    {
        if (!$this->template)
            $this->setTemplate(self::DEFAULT_TEMPLATE);

        return $this->template;
    }

    /**
     * @param string $template
     *
     * @return $this
     */
    function setTemplate($template)
    {
        $this->template = (string) $template;

        return $this;
    }
}
