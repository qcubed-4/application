<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Action;

use QCubed\Exception\Caller;
use QCubed\Control\ControlBase;
use QCubed\QString;

/**
 * Class JavaScript
 *
 * Client-side action - no postbacks of any kind are performed.
 * All handling activity happens in Javascript.
 *
 * @was QJavaScriptAction
 * @package QCubed\Action
 */
class JavaScript extends ActionBase
{
    /** @var string JS to be run on the client side */
    protected $strJavaScript;

    /**
     * The constructor
     * @param string $strJavaScript JS which is to be executed on the client side
     */
    public function __construct($strJavaScript)
    {
        $this->strJavaScript = trim($strJavaScript);
        if (QString::lastCharacter($this->strJavaScript) == ';') {
            $this->strJavaScript = substr($this->strJavaScript, 0, strlen($this->strJavaScript) - 1);
        }
    }

    /**
     * PHP Magic function to get the property values of a class object
     *
     * @param string $strName Name of the property
     * @return mixed|null|string
     * @throws Caller
     */
    public function __get($strName)
    {
        switch ($strName) {
            case 'JavaScript':
                return $this->strJavaScript;
            default:
                try {
                    return parent::__get($strName);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }

    /**
     * Returns the JS which will be executed on the client side
     *
     * @param ControlBase $objControl
     * @return string
     */
    public function renderScript(ControlBase $objControl)
    {
        return sprintf('%s;', $this->strJavaScript);
    }
}
