<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Control;

use QCubed\Css\DisplayType;

/**
 * Class Panel
 *
 * Panels can be used to create composite controls which are to be rendered as blocks (not inline)
 *
 * @package QCubed\Control
 */
class Panel extends BlockControl
{
    ///////////////////////////
    // Protected Member Variables
    ///////////////////////////
    /** @var string HTML tag to the used for the Block Control */
    protected string $strTagName = 'div';
    /** @var string Default display style for the control */
    protected string $strDefaultDisplayStyle = DisplayType::BLOCK;
    /** @var bool Is the control a block element? */
    protected bool $blnIsBlockElement = true;
    /** @var bool Use htmlentities for the control? */
    protected bool $blnHtmlEntities = false;
}