<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Action;

use QCubed\Control\ControlBase;
use QCubed\Exception\Caller;

/**
 * Class Redirect
 *
 * Client-side action - no postbacks of any kind are performed.
 * All handling activity happens in Javascript.
 *
 * @was QRedirectAction
 * @package QCubed\Action
 */
class Redirect extends ActionBase
{
    /** @var string JS to be run on the client side */
    protected $strJavaScript;

    /**
     * possible values:
     * http://google.com
     * index.php?page=view
     * /foo/bar/woot.html
     *
     * @param string $strUrl
     */
    public function __construct($strUrl)
    {
        $this->strJavaScript = sprintf("document.location.href ='%s'", trim($strUrl));
    }

    /**
     * PHP Magic function to get the property values of a class object
     *
     * @param string $strName Name of the property
     *
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
     * Returns the JS which runs on the client side
     * @param ControlBase $objControl
     *
     * @return string
     */
    public function renderScript(ControlBase $objControl)
    {
        return sprintf('%s;', $this->strJavaScript);
    }
}
