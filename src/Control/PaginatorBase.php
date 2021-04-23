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

use QCubed as Q;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Type;

/**
 * This controls works together with a PaginatedControl to implement a paginator for that control. Multiple
 * paginators per PaginatedControl can be declared.
 *
 * @property integer      $ItemsPerPage        How many items you want to display per page when Pagination is enabled
 * @property integer      $PageNumber          The current page number you are viewing. 1 is the first page, there is no page zero.
 * @property integer      $TotalItemCount      The total number of items in the ENTIRE recordset -- only used when Pagination is enabled
 * @property boolean      $UseAjax             Whether to use ajax in the drawing.
 * @property-read integer $PageCount           Current number of pages being represented
 * @property mixed        $WaitIcon            The wait icon to display
 * @property-read mixed   $PaginatedControl    The paginated control linked to this control
 * @property integer      $IndexCount          The maximum number of page numbers to disply in the paginator
 * @property string       LabelForPrevious     Label to be used for the 'Previous' link.
 * @property string       LabelForNext         Label to be used for the 'Next' link.
 * @was QPaginatorBase
 * @package QCubed\Control
 */
abstract class PaginatorBase extends Q\Project\Control\ControlBase
{
    /** @var string Label for the 'Previous' link */
    protected $strLabelForPrevious;
    /** @var string Label for the 'Next' link */
    protected $strLabelForNext;

    // BEHAVIOR
    /** @var int Default number of items per page */
    protected $intItemsPerPage = 15;
    /** @var int Default page number (to begin rendering with) */
    protected $intPageNumber = 1;
    /** @var int Default item count for the paginator */
    protected $intTotalItemCount = 0;
    /** @var bool Should switching the pages happen over AJAX or Server call (page reload) */
    protected $blnUseAjax = true;
    /** @var  PaginatedControl The control which is going to be paginated with the paginator */
    protected $objPaginatedControl;
    /** @var string Default Wait Icon to be used */
    protected $objWaitIcon = 'default';
    /** @var int Number of index items in the paginator to display */
    protected $intIndexCount = 10;


    /** @var null|Proxy  */
    protected $prxPagination = null;

    // SETUP
    /** @var bool  */
    protected $blnIsBlockElement = false;
    /** @var string The tag element inside which the paginator has to be rendered */
    protected $strTag = 'span';

    //////////
    // Methods
    //////////
    /**
     * Constructor method
     *
     * @param ControlBase|FormBase $objParentObject
     * @param null|string                     $strControlId
     *
     * @throws Caller
     */
    public function __construct($objParentObject, $strControlId = null)
    {
        try {
            parent::__construct($objParentObject, $strControlId);
        } catch (Caller  $objExc) {
            $objExc->incrementOffset();
            throw $objExc;
        }

        $this->prxPagination = new Q\Control\Proxy($this);
        $this->strLabelForPrevious = t('Previous');
        $this->strLabelForNext = t('Next');

        $this->setup();
    }

    /**
     * Setup the proxy events.
     */
    protected function setup()
    {
        // Setup Pagination Events
        $this->prxPagination->removeAllActions( Q\Event\Click::EVENT_NAME);
        if ($this->blnUseAjax) {
            $this->prxPagination->addAction(new Q\Event\Click(), new Q\Action\AjaxControl($this, 'pageClick'));
        } else {
            $this->prxPagination->addAction(new Q\Event\Click(), new Q\Action\ServerControl($this, 'pageClick'));
        }
        $this->prxPagination->addAction(new Q\Event\Click, new Q\Action\Terminate());
    }

    public function parsePostData()
    {
    }

    /**
     * Validates the control.
     *
     * For now, it simply returns true
     *
     * @return bool
     */
    public function validate()
    {
        return true;
    }

    /**
     * Respond to the pageClick event
     *
     * @param array $params
     */
    public function pageClick(Q\Action\ActionParams $params)
    {
        $this->objPaginatedControl->PageNumber = Type::cast($params->Param, Type::INTEGER);
    }

    /**
     * Assign a paginated control to the paginator.
     *
     * @param PaginatedControl $objPaginatedControl
     */
    public function setPaginatedControl(Q\Control\PaginatedControl $objPaginatedControl)
    {
        $this->objPaginatedControl = $objPaginatedControl;

        $this->UseAjax = $objPaginatedControl->UseAjax;
        $this->ItemsPerPage = $objPaginatedControl->ItemsPerPage;
        $this->PageNumber = $objPaginatedControl->PageNumber;
        $this->TotalItemCount = $objPaginatedControl->TotalItemCount;
    }


    /**
     * Renders the set of previous buttons. This would be whatever comes before the page numbers in the paginator.
     * This particular implementation renders a "Previous" text button, with a separator, and a Rewind button
     * that looks like a number followed by an ellipsis.
     *
     * @return string
     */
    protected function getPreviousButtonsHtml()
    {
        if ($this->intPageNumber <= 1) {
            $strPrevious = $this->strLabelForPrevious;
        } else {
            $mixActionParameter = $this->intPageNumber - 1;
            $strPrevious = $this->prxPagination->renderAsLink($this->strLabelForPrevious, $mixActionParameter, ['id'=>$this->ControlId . "_arrow_" . $mixActionParameter]);
        }

        $strToReturn = sprintf('<span class="arrow previous">%s</span><span class="break">|</span>', $strPrevious);

        list($intPageStart, $intPageEnd) = $this->calcBunch();

        if ($intPageStart != 1) {
            $strToReturn .= $this->getPageButtonHtml(1);
            $strToReturn .= '<span class="ellipsis">&hellip;</span>';
        }

        return $strToReturn;
    }


    /**
     * Return the html for a particular page button.
     *
     * @param $intIndex
     * @return string
     */
    protected function getPageButtonHtml($intIndex)
    {
        if ($this->intPageNumber == $intIndex) {
            $strToReturn = sprintf('<span class="selected">%s</span>', $intIndex);
        } else {
            $mixActionParameter = $intIndex;
            $strToReturn = $this->prxPagination->renderAsLink($intIndex, $mixActionParameter, ['id'=>$this->ControlId . "_page_" . $mixActionParameter]);
            $strToReturn = sprintf('<span class="page">%s</span>', $strToReturn);
        }
        return $strToReturn;
    }

    /**
     * Returns the HTML for the group of buttons that come after the group of page buttons.
     * @return string
     */
    protected function getNextButtonsHtml()
    {
        list($intPageStart, $intPageEnd) = $this->calcBunch();

        // build it backwards

        $intPageCount = $this->PageCount;
        if ($this->intPageNumber >= $intPageCount) {
            $strNext = $this->strLabelForNext;
        } else {
            $mixActionParameter = $this->intPageNumber + 1;
            $strNext = $this->prxPagination->renderAsLink($this->strLabelForNext, $mixActionParameter, ['id'=>$this->ControlId . "_arrow_" . $mixActionParameter]);
        }

        $strToReturn = sprintf('<span class="arrow next">%s</span>', $strNext);

        $strToReturn = '<span class="break">|</span>' . $strToReturn;

        if ($intPageEnd != $intPageCount) {
            $strToReturn = $this->getPageButtonHtml($intPageCount) . $strToReturn;
            $strToReturn = '<span class="ellipsis">&hellip;</span>' . $strToReturn;
        }

        return $strToReturn;
    }

    /**
     * Returns the HTML for rendering the control
     *
     * @return string HTML for the control
     * @throws Caller
     */
    public function getControlHtml()
    {
        $this->objPaginatedControl->dataBind();

        $strToReturn = $this->getPreviousButtonsHtml();

        list($intPageStart, $intPageEnd) = $this->calcBunch();

        for ($intIndex = $intPageStart; $intIndex <= $intPageEnd; $intIndex++) {
            $strToReturn .= $this->getPageButtonHtml($intIndex);
        }

        $strToReturn .= $this->getNextButtonsHtml();

        $strStyle = $this->getStyleAttributes();
        if ($strStyle) {
            $strStyle = sprintf(' style="%s"', $strStyle);
        }

        // Wrap the whole paginator in the main control tag
        $strToReturn = sprintf('<%s id="%s" %s%s>%s</%s>', $this->strTag, $this->strControlId, $strStyle, $this->renderHtmlAttributes(), $strToReturn, $this->strTag);

        return $strToReturn;
    }

    /**
     * Calculates the start and end of the center bunch of the paginator. If the start is not 1, then we know
     * we need to add a first page item too. If the end of the bunch is not the last page, then we need to add a last page item.
     * Returns an array that has the start and end of the center bunch.
     * @return int[]
     */
    protected function calcBunch()
    {
        /**
         * "Bunch" is defined as the collection of numbers that lies in between the pair of Ellipsis ("...")
         *
         * LAYOUT
         *
         * For IndexCount of 10
         * 2   213   2 (two items to the left of the bunch, and then 2 indexes, selected index, 3 indexes, and then two items to the right of the bunch)
         * e.g. 1 ... 5 6 *7* 8 9 10 ... 100
         *
         * For IndexCount of 11
         * 2   313   2
         *
         * For IndexCount of 12
         * 2   314   2
         *
         * For IndexCount of 13
         * 2   414   2
         *
         * For IndexCount of 14
         * 2   415   2
         *
         *
         *
         * START/END PAGE NUMBERS FOR THE BUNCH
         *
         * For IndexCount of 10
         * 1 2 3 4 5 6 7 8 .. 100
         * 1 .. 4 5 *6* 7 8 9 .. 100
         * 1 .. 92 93 *94* 95 96 97 .. 100
         * 1 .. 93 94 95 96 97 98 99 100
         *
         * For IndexCount of 11
         * 1 2 3 4 5 6 7 8 9 .. 100
         * 1 .. 4 5 6 *7* 8 9 10 .. 100
         * 1 .. 91 92 93 *94* 95 96 97 .. 100
         * 1 .. 92 93 94 95 96 97 98 99 100
         *
         * For IndexCount of 12
         * 1 2 3 4 5 6 7 8 9 10 .. 100
         * 1 .. 4 5 6 *7* 8 9 10 11 .. 100
         * 1 .. 90 91 92 *93* 94 95 96 97 .. 100
         * 1 .. 91 92 93 94 95 96 97 98 99 100
         *
         * For IndexCount of 13
         * 1 2 3 4 5 6 7 8 9 11 .. 100
         * 1 .. 4 5 6 7 *8* 9 10 11 12 .. 100
         * 1 .. 89 90 91 92 *93* 94 95 96 97 .. 100
         * 1 .. 90 91 92 93 94 95 96 97 98 99 100
         */

        $intPageCount = $this->PageCount;

        if ($intPageCount <= $this->intIndexCount) {
            // no bunches needed
            $intPageStart = 1;
            $intPageEnd = $intPageCount;
        } else {
            $intMinimumEndOfBunch = min($this->intIndexCount - 2, $intPageCount);
            $intMaximumStartOfBunch = max($intPageCount - $this->intIndexCount + 3, 1);

            $intLeftOfBunchCount = floor(($this->intIndexCount - 5) / 2);
            $intRightOfBunchCount = round(($this->intIndexCount - 5.0) / 2.0);

            $intLeftBunchTrigger = 4 + $intLeftOfBunchCount;
            $intRightBunchTrigger = $intMaximumStartOfBunch + round(($this->intIndexCount - 8.0) / 2.0);

            if ($this->intPageNumber < $intLeftBunchTrigger) {
                $intPageStart = 1;
            } else {
                $intPageStart = min($intMaximumStartOfBunch, $this->intPageNumber - $intLeftOfBunchCount);
            }

            if ($this->intPageNumber > $intRightBunchTrigger) {
                $intPageEnd = $intPageCount;
            } else {
                $intPageEnd = max($intMinimumEndOfBunch, $this->intPageNumber + $intRightOfBunchCount);
            }
        }
        return [$intPageStart, $intPageEnd];
    }

    /**
     * After adjusting the total item count, or page size, or other parameters, call this to adjust the page number
     * to make sure it is not off the end.
     */
    public function limitPageNumber()
    {
        $pageCount = $this->calcPageCount();
        if ($this->intPageNumber > $pageCount) {
            if ($pageCount <= 1) {
                $this->intPageNumber = 1;
            } else {
                $this->intPageNumber = $pageCount;
            }
        }
    }

    /**
     * Calculates the total number of pages for the paginator
     *
     * @return float Number of pages
     */
    public function calcPageCount()
    {
        $intCount = (int) floor($this->intTotalItemCount / $this->intItemsPerPage) +
            ((($this->intTotalItemCount % $this->intItemsPerPage) != 0) ? 1 : 0);
        return $intCount;
    }

    /**
     * PHP magic method to get property value
     *
     * @param string $strName Name of the property
     *
     * @return mixed
     *
     * @throws Caller
     */
    public function __get($strName)
    {
        switch ($strName) {
            // BEHAVIOR
            case "ItemsPerPage": return $this->intItemsPerPage;
            case "PageNumber": return $this->intPageNumber;
            case "TotalItemCount": return $this->intTotalItemCount;
            case "UseAjax": return $this->blnUseAjax;
            case "PageCount":
                return $this->calcPageCount();
            case 'WaitIcon':
                return $this->objWaitIcon;
            case "PaginatedControl":
                return $this->objPaginatedControl;
            case 'IndexCount':
                return $this->intIndexCount;
            case 'LabelForNext':
                return $this->strLabelForNext;
            case 'LabelForPrevious':
                return $this->strLabelForPrevious;
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
     * PHP magic method to set the value of property of class
     *
     * @param string $strName
     * @param string $mixValue
     *
     * @return void
     * @throws Caller
     * @throws InvalidCast
     */
    public function __set($strName, $mixValue)
    {
        $this->blnModified = true;

        switch ($strName) {
            // BEHAVIOR
            case "ItemsPerPage":
                try {
                    if ($mixValue > 0) {
                        $this->intItemsPerPage = Type::cast($mixValue, Type::INTEGER);
                    } else {
                        $this->intItemsPerPage = 10;
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "PageNumber":
                try {
                    $intNewPageNum = Type::cast($mixValue, Type::INTEGER);
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                if ($intNewPageNum > 1) {
                    $this->intPageNumber = $intNewPageNum;
                } else {
                    $this->intPageNumber = 1;
                }
                break;

            case "TotalItemCount":
                try {
                    if ($mixValue > 0) {
                        $this->intTotalItemCount = Type::cast($mixValue, Type::INTEGER);
                    } else {
                        $this->intTotalItemCount = 0;
                    }
                    $this->limitPageNumber();
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "UseAjax":
                try {
                    $this->blnUseAjax = Type::cast($mixValue, Type::BOOLEAN);
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

                // Because we are switching to/from Ajax, we need to reset the events
                $this->setup();
                break;

            case 'WaitIcon':
                try {
                    $this->objWaitIcon = $mixValue;
                    //ensure we update our ajax action to use it
                    $this->setup();
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;

            case 'IndexCount':
                $this->intIndexCount = Type::cast($mixValue, Type::INTEGER);
                if ($this->intIndexCount < 7) {
                    throw new Caller('Paginator must have an IndexCount >= 7');
                }
                break;

            case 'LabelForNext':
                try {
                    $this->strLabelForNext = Type::cast($mixValue, Type::STRING);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;

            case 'LabelForPrevious':
                try {
                    $this->strLabelForPrevious = Type::cast($mixValue, Type::STRING);
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
                break;
        }
    }
}
