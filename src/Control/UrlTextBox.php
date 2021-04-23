<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Control;

require_once(dirname(dirname(__DIR__)) . '/i18n/i18n-lib.inc.php');
use QCubed\Application\t;

use QCubed\Project\Control\TextBox;

/**
 * Class UrlTextBox
 *
 * A subclass of TextBox that validates and sanitizes urls.
 * @was QUrlTextBox
 * @package QCubed\Control
 */
class UrlTextBox extends TextBox
{
    /** @var int */
    protected $intSanitizeFilter = FILTER_SANITIZE_URL;
    /** @var int */
    protected $intValidateFilter = FILTER_VALIDATE_URL;

    /**
     * Constructor
     *
     * @param ControlBase|FormBase $objParentObject
     * @param null|string $strControlId
     */
    public function __construct($objParentObject, $strControlId = null)
    {
        parent::__construct($objParentObject, $strControlId);
        $this->strLabelForInvalid = t('Invalid Web Address');
        $this->strTextMode = self::URL;
    }
}