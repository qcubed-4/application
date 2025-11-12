<?php

use QCubed\Control\ControlBase;
use QCubed\Control\FormBase;
use QCubed\Exception\Caller;
use QCubed\QString;
use QCubed\Type;

/**
 * This is a SAMPLE of a custom Control that you could write.  Think of this as a "starting point".
 * Remember: EVERYTHING here is meant to be customized!  To use, simply make a copy of this file,
 * rename the file, and edit the renamed file.  Remember to specify a control Class name which matches the
 * name of your file.  And then implement your own logic for getControlHtml().
 *
 *
 */
class SampleControl extends ControlBase
{
    protected int $intExample;
    protected string $strFoo;

    /**
     * Constructor for initializing the control with the parent object and optional control ID.
     *
     * @param ControlBase|FormBase $objParentObject The parent object to which this control belongs.
     * @param string|null $strControlId An optional control ID to uniquely identify the control.
     * @throws Caller
     */
    public function __construct(ControlBase|FormBase $objParentObject, ?string $strControlId = null)
    {
        try {
            parent::__construct($objParentObject, $strControlId);
        } catch (Caller $objExc) {
            $objExc->incrementOffset();
            throw $objExc;
        }

        // Set up Control-specific CSS and JS files to be loaded
        // Paths are relative to the __CSS_ASSETS__ and __JS_ASSETS__ directories
        // Multiple files can be specified, as well, by separating with a comma
//			$this->strJavaScripts = 'custom.js, ../path/to/prototype.js, etc.js';
//			$this->strStyleSheets = 'custom.css';

        // Additional Setup Performed here
        $this->intExample = 28;
        $this->strFoo = 'Hello!';
    }

    /**
     * Processes and parses the post-data from an HTTP request.
     * @return void
     */
    public function parsePostData(): void
    {
    }

    /**
     * Validates the current state of the object or operation.
     *
     * @return bool Returns true if the validation is successful, false otherwise.
     */
    public function validate(): bool
    {
        return true;
    }

    /**
     * Renders the control's HTML representation.
     *
     * @return string The HTML markup for the control.
     */
    public function getControlHtml(): string
    {
        return $this->renderTag('span', null, null, $this->getInnerHtml());
    }

    /**
     * Retrieves the inner HTML content for the control with properly escaped entities.
     *
     * @return string The escaped inner HTML content as a string.
     */
    protected function getInnerHtml(): string
    {
        return QString::htmlEntities("Sample Control " . $this->intExample . ' - ' . $this->strFoo);
    }


    // For any JavaScript calls that need to be made whenever this control is rendered or re-rendered
//		public function getEndScript() {
//			$strToReturn = parent::getEndScript();
//			return $strToReturn;
//		}

    // For any HTML code that needs to be rendered at the END of the QForm when this control is INITIALLY rendered.
//		public function getEndHtml() {
//			$strToReturn = parent::getEndHtml();
//			return $strToReturn;
//		}

    /////////////////////////
    // Public Properties: GET
    /////////////////////////
    /**
     * Magic method to retrieve the value of a property by its name.
     *
     * @param string $strName The name of the property to retrieve.
     * @return mixed The value of the requested property, or the result of the parent::__get() method if applicable.
     * @throws Caller If the property does not exist and cannot be retrieved.
     */
    public function __get(string $strName): mixed
    {
        switch ($strName) {
            case 'Example':
                return $this->intExample;
            case 'Foo':
                return $this->strFoo;

            default:
                try {
                    return parent::__get($strName);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }

    /////////////////////////
    // Public Properties: SET
    /////////////////////////
    /**
     * Handles setting of property values dynamically.
     *
     * @param string $strName The name of the property to set.
     * @param mixed $mixValue The value to assign to the property.
     * @return void
     * @throws Caller If the property name or value type is invalid.
     */
    public function __set(string $strName, mixed $mixValue): void
    {
        $this->blnModified = true;

        switch ($strName) {

            case 'Example':
                try {
                    $this->intExample = Type::cast($mixValue, Type::INTEGER);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;
            case 'Foo':
                try {
                    $this->strFoo = Type::cast($mixValue, Type::STRING);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;

            default:
                try {
                    parent::__set($strName, $mixValue);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }
}
