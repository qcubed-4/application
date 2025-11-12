<?php
    /**
     *
     * Part of the QCubed PHP framework.
     *
     * @license MIT
     *
     */

    namespace QCubed\Control;

    require_once(dirname(__DIR__, 2) . '/i18n/i18n-lib.inc.php');

    use Exception;
    use QCubed as Q;
    use QCubed\Action\ActionParams;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Type;

    /**
     * This control works with PaginatedControl to implement pagination for this control. Multiple
     * Page numbers can be declared per PaginatedControl.
     *
     * @property integer      $ItemsPerPage        How many items you want to display per a page when Pagination is enabled
     * @property integer      $PageNumber          The current page number you are viewing. 1 is the first page, there is no page zero.
     * @property integer      $TotalItemCount      The total number of items in the ENTIRE recordset -- only used when Pagination is enabled
     * @property boolean      $UseAjax             Whether to use ajax in the drawing.
     * @property-read integer $PageCount           The Current number of pages being represented
     * @property mixed        $WaitIcon            The wait icon to display
     * @property-read mixed   $PaginatedControl    The paginated control linked to this control
     * @property integer      $IndexCount          The maximum number of page numbers to display in the paginator
     * @property string       LabelForPrevious     Label to be used for the 'Previous' link.
     * @property string       LabelForNext         Label to be used for the 'Next' link.
     *
     * @package QCubed\Control
     */
    abstract class PaginatorBase extends Q\Project\Control\ControlBase
    {
        /** @var string Label for the 'Previous' link */
        protected string $strLabelForPrevious;
        /** @var string Label for the 'Next' link */
        protected string $strLabelForNext;

        // BEHAVIOR
        /** @var int Default number of items per page */
        protected int $intItemsPerPage = 15;
        /** @var int Default page number (to begin rendering with) */
        protected int $intPageNumber = 1;
        /** @var int Default item count for the paginator */
        protected int $intTotalItemCount = 0;
        /** @var bool If page switching should be done via AJAX or server call (page reload) */
        protected bool $blnUseAjax = true;
        /** @var  PaginatedControl The control which is going to be paginated with the paginator */
        protected PaginatedControl $objPaginatedControl;
        /** @var string Default Wait Icon to be used */
        protected string $objWaitIcon = 'default';
        /** @var int Number of index items in the paginator to display */
        protected int $intIndexCount = 10;


        /** @var null|Proxy  */
        protected ?Proxy $prxPagination = null;

        // SETUP
        /** @var bool  */
        protected bool $blnIsBlockElement = false;
        /** @var string The tag element inside which the paginator has to be rendered */
        protected string $strTag = 'span';

        //////////
        // Methods
        //////////
        /**
         * Constructor method
         *
         * @param ControlBase|FormBase $objParentObject
         * @param string|null $strControlId
         *
         * @throws Caller
         * @throws \Exception
         */
        public function __construct(FormBase|ControlBase $objParentObject, ?string $strControlId = null)
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
         * Configure the pagination setup by attaching appropriate events and actions.
         *
         * This method sets up the pagination control by first clearing any existing actions
         * linked to the click event and then adds new actions based on the configuration.
         * The actions include either an AJAX-based or server-based page-click handler,
         * followed by a terminate action.
         *
         * @throws Caller
         */
        protected function setup(): void
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

        /**
         * Processes and parses post data from the request.
         *
         * @return void
         */
        public function parsePostData(): void
        {
        }

        /**
         * Validates the control.
         *
         * For now, it simply returns true
         *
         * @return bool
         */
        public function validate(): bool
        {
            return true;
        }

        /**
         * Respond to the pageClick event
         *
         * @param ActionParams $params
         * @throws Caller
         * @throws InvalidCast
         */
        public function pageClick(Q\Action\ActionParams $params): void
        {
            $this->objPaginatedControl->PageNumber = Type::cast($params->Param, Type::INTEGER);
        }

        /**
         * Assign a paginated control to the paginator.
         *
         * @param PaginatedControl $objPaginatedControl
         */
        public function setPaginatedControl(Q\Control\PaginatedControl $objPaginatedControl): void
        {
            $this->objPaginatedControl = $objPaginatedControl;

            $this->UseAjax = $objPaginatedControl->UseAjax;
            $this->ItemsPerPage = $objPaginatedControl->ItemsPerPage;
            $this->PageNumber = $objPaginatedControl->PageNumber;
            $this->TotalItemCount = $objPaginatedControl->TotalItemCount;
        }

        /**
         * Generate the HTML for the previous page buttons in pagination
         *
         * This method creates the HTML string for the "previous" navigation buttons,
         * including the conditional logic for the first page and preparing links for earlier pages.
         * It also calculates and appends additional elements like ellipsis for skipped pages.
         *
         * @return string The generated HTML string for the previous buttons and related elements
         */
        protected function getPreviousButtonsHtml(): string
        {
            if ($this->intPageNumber <= 1) {
                $strPrevious = $this->strLabelForPrevious;
            } else {
                $mixActionParameter = $this->intPageNumber - 1;
                $strPrevious = $this->prxPagination->renderAsLink($this->strLabelForPrevious, $mixActionParameter, ['id' => $this->ControlId . "_arrow_" . $mixActionParameter]);
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
         * Generate the HTML for a pagination button
         *
         * @param int $intIndex The index of the page button to generate
         * @return string The HTML string representing the page button
         */
        protected function getPageButtonHtml(int $intIndex): string
        {
            if ($this->intPageNumber == $intIndex) {
                $strToReturn = sprintf('<span class="selected">%s</span>', $intIndex);
            } else {
                $mixActionParameter = $intIndex;
                $strToReturn = $this->prxPagination->renderAsLink($intIndex, $mixActionParameter, ['id' => $this->ControlId . "_page_" . $mixActionParameter]);
                $strToReturn = sprintf('<span class="page">%s</span>', $strToReturn);
            }
            return $strToReturn;
        }

        /**
         * Generates the HTML for the next buttons in the pagination control.
         *
         * @return string Returns the generated HTML string for the next pagination buttons.
         */
        protected function getNextButtonsHtml(): string
        {
            list($intPageStart, $intPageEnd) = $this->calcBunch();

            // build it backwards

            $intPageCount = $this->PageCount;
            if ($this->intPageNumber >= $intPageCount) {
                $strNext = $this->strLabelForNext;
            } else {
                $mixActionParameter = $this->intPageNumber + 1;
                $strNext = $this->prxPagination->renderAsLink($this->strLabelForNext, $mixActionParameter, ['id' => $this->ControlId . "_arrow_" . $mixActionParameter]);
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
        public function getControlHtml(): string
        {
            $this->objPaginatedControl->dataBind();

            $strPaginatorHtml = $this->getPreviousButtonsHtml();

            list($intPageStart, $intPageEnd) = $this->calcBunch();

            for ($intIndex = $intPageStart; $intIndex <= $intPageEnd; $intIndex++) {
                $strPaginatorHtml .= $this->getPageButtonHtml($intIndex);
            }

            $strPaginatorHtml .= $this->getNextButtonsHtml();

            $strStyle = $this->getStyleAttributes();
            if ($strStyle) {
                $strStyle = sprintf(' style="%s"', $strStyle);
            }

            // Let's put all the HTML in ONLY ONE wrapper:
            return sprintf('<%s id="%s"%s%s>%s</%s>', $this->strTag, $this->strControlId, $strStyle, $this->renderHtmlAttributes(), $strPaginatorHtml, $this->strTag);
        }

        /**
         * Calculates the start and end of the center bunch of the paginator. If the start is not 1, then we know
         * we need to add a first page item too. If the end of the bunch is not the last page, then we need to add a last-page item.
         * Returns an array that has the start and end of the center bunch.
         * @return int[]
         */
        protected function calcBunch(): array
        {
            /**
             * "Bunch" is defined as the collection of numbers that lies in between the pair of Ellipsis ("...")
             *
             * LAYOUT
             *
             * For IndexCount of 10
             * 2 213 2 (two items to the left of the bunch, and then 2 indexes, selected index, 3 indexes, and then two items to the right of the bunch)
             * e.g., 1 ... 5 6 *7* 8 9 10 ... 100
             *
             * For IndexCount of 11
             * 2 313 2
             *
             * For IndexCount of 12
             * 2 314 2
             *
             * For IndexCount of 13
             * 2 414 2
             *
             * For IndexCount of 14
             * 2 415 2
             *
             *
             *
             * START/END PAGE NUMBERS FOR THE BUNCH
             *
             * For IndexCount of 10
             * 1 2 3 4 5 6 7 8 ... 100
             * 1 ... 4 5 *6* 7 8 9 ... 100
             * 1 ... 92 93 *94* 95 96 97 ... 100
             * 1 ... 93 94 95 96 97 98 99 100
             *
             * For IndexCount of 11
             * 1 2 3 4 5 6 7 8 9 ... 100
             * 1 ... 4 5 6 *7* 8 9 10 ... 100
             * 1 ... 91 92 93 *94* 95 96 97 ... 100
             * 1 ... 92 93 94 95 96 97 98 99 100
             *
             * For IndexCount of 12
             * 1 2 3 4 5 6 7 8 9 10 ... 100
             * 1 ... 4 5 6 *7* 8 9 10 11 ... 100
             * 1 ... 90 91 92 *93* 94 95 96 97 ... 100
             * 1 ... 91 92 93 94 95 96 97 98 99 100
             *
             * For IndexCount of 13
             * 1 2 3 4 5 6 7 8 9 11 ... 100
             * 1 ... 4 5 6 7 *8* 9 10 11 12 ... 100
             * 1 ... 89 90 91 92 *93* 94 95 96 97 ... 100
             * 1 ... 90 91 92 93 94 95 96 97 98 99 100
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
        public function limitPageNumber(): void
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
         * Calculate the total number of pages based on the total item count and items per page
         *
         * @return float|int The calculated number of pages
         */
        public function calcPageCount(): float|int
        {
            return (int) floor($this->intTotalItemCount / $this->intItemsPerPage) +
                ((($this->intTotalItemCount % $this->intItemsPerPage) != 0) ? 1 : 0);
        }

        /**
         * PHP magic method to get property value
         *
         * @param string $strName Name of the property
         *
         * @return mixed
         *
         * @throws Caller
         * @throws \Exception
         */
        public function __get(string $strName): mixed
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
         * @param mixed $mixValue
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws Exception
         */
        public function __set(string $strName, mixed $mixValue): void
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
