<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Control;

use QCubed as Q;

/**
 * Class Label
 *
 * QLabel class is used to create text on the client side.
 * By default it will not accept raw HTML for text.
 * Set Htmlentities to false to enable that behavior
 *
 * @was QLabel
 * @package QCubed\Control
 */
class Label extends BlockControl
{
    ///////////////////////////
    // Protected Member Variables
    ///////////////////////////
    /** @var string HTML tag to be used when rendering this control */
    protected $strTagName = 'span';
    /** @var bool Should htmlentities be run on the contents of this control? */
    protected $blnHtmlEntities = true;

    /**
     * Returns the generator corresponding to this control.
     *
     * @return Q\Codegen\Generator\GeneratorBase
     */
    public static function getCodeGenerator() {
        return new Q\Codegen\Generator\Label(__CLASS__);
    }
}
