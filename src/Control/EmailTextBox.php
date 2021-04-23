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
 * A subclass of TextBox that validates and sanitizes emails.
 *
 * @was QEmailTextBox
 * @package QCubed\Control
 */
class EmailTextBox extends TextBox
{
    /** @var int */
    protected $intSanitizeFilter = FILTER_SANITIZE_EMAIL;
    /** @var int */
    protected $intValidateFilter = FILTER_VALIDATE_EMAIL;

    public function __construct($objParentObject, $strControlId = null)
    {
        parent::__construct($objParentObject, $strControlId);
        $this->strLabelForInvalid = t('Invalid Email Address');
        $this->strTextMode = self::EMAIL;
    }
}