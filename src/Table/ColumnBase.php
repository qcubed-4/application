<?php
    /**
     *
     * Part of the QCubed PHP framework.
     *
     * @license MIT
     *
     */

    namespace QCubed\Table;

    use QCubed as Q;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\ObjectBase;
    use QCubed\Project\Application;
    use QCubed\TagStyler;
    use QCubed\Type;
    use QCubed\Control\FormBase;
    use QCubed\Project\Control\ControlBase;
    use QCubed\Control\TableBase;

    /**
     * Class ColumnBase
     *
     * Represents a column for a Table control. Different subclasses (see below) allow accessing and fetching the data
     * for each cell in a variety of ways
     *
     * @property string                 $Name           name of the column
     * @property string                 $CssClass       CSS class of the column. This will be applied to every cell in the column. Use Col type
     * 													to set the class for the actual 'col' tag if using col tags.
     * @property string                 $HeaderCssClass CSS class of the column's cells when it's rendered in a table header
     * @property boolean                $HtmlEntities   if true, cell values will be converted using htmlentities()
     * @property boolean                $RenderAsHeader if true, all cells in the column will be rendered with a <<th>> tag instead of <<td>>
     * @property integer                $Id             HTML Id attribute to put in the col tag
     * @property integer                $Span           HTML span attribute to put in the col tag
     * @property-read TableBase  $ParentTable    parent table of the column
     * @property-write TableBase $_ParentTable   Parent table of this column
     * @property-write callable $CellParamsCallback A callback to set the HTML parameters of a generated cell
     * @property boolean                $Visible        Whether the column will be drawn. Defaults to true.
     * @property-read TagStyler		$CellStyler		The tag styler for the cells in the column
     * @property-read TagStyler		$HeaderCellStyler		The tag styler for the header cells in the column
     * @property-read TagStyler		$ColStyler		The tag styler for the col tag in the column
     * @package QCubed\Table
     */
    abstract class ColumnBase extends ObjectBase
    {
        /** @var string */
        protected string $strName;
        /** @var string|null */
        protected ?string $strCssClass = null;
        /** @var string|null */
        protected ?string $strHeaderCssClass = null;
        /** @var boolean */
        protected bool $blnHtmlEntities = true;
        /** @var boolean|null */
        protected ?bool $blnRenderAsHeader = false;
        /** @var TableBase|null */
        protected ?TableBase $objParentTable = null;
        /** @var integer */
        protected int $intSpan = 1;
        /** @var string|null an optional ID for column tag rendering and datatables */
        protected ?string $strId = null;
        /** @var bool Easy way to hide a column without removing the column. */
        protected bool $blnVisible = true;
        /** @var callable Callback to modify the HTML attributes of the generated cell. */
        protected $cellParamsCallback = null;
        /** @var TagStyler Styles for each cell. Usually this should be done in CSS for efficient code generation. */
        protected TagStyler $objCellStyler;
        /** @var TagStyler Styles for each header cell. Usually this should be done in CSS for efficient code generation. */
        protected TagStyler $objHeaderCellStyler;
        /** @var TagStyler Styles for each col. Usually this should be done in CSS for efficient code generation. */
        protected TagStyler $objColStyler;

        /**
         * @param string $strName Name of the column
         */
        public function __construct(string $strName)
        {
            $this->strName = $strName;

            $this->objCellStyler = new TagStyler();
            $this->objHeaderCellStyler = new TagStyler();
        }

        /**
         *
         * Render the header cell including opening and closing tags.
         *
         * This will be called by the data table if ShowHeader is on and will only
         * be called for the top line item.
         *
         */
        public function renderHeaderCell(): ?string
        {
            if (!$this->blnVisible) {
                return '';
            }

            $cellValue = $this->fetchHeaderCellValue();
            if ($this->blnHtmlEntities) {
                $cellValue = Q\QString::htmlEntities($cellValue);
            }
            if ($cellValue == '' && Application::instance()->context()->isBrowser(Q\Context::INTERNET_EXPLORER)) {
                $cellValue = '&nbsp;';
            }

            return Q\Html::renderTag('th', $this->getHeaderCellParams(), $cellValue);
        }

        /**
         * Returns the text to print in the header cell if one is to be drawn. Override if you want
         * something other than the default.
         */
        public function fetchHeaderCellValue(): string
        {
            return $this->strName;
        }

        /**
         * Returns an array of key/value pairs to insert as parameters in the header cell. Override and add
         * more if you need them.
         * @return array
         */
        public function getHeaderCellParams(): array
        {
            $aParams['scope'] = 'col';
            if ($this->strHeaderCssClass) {
                $aParams['class'] = $this->strHeaderCssClass;
            }
            return $this->objHeaderCellStyler->getHtmlAttributes($aParams);
        }

        /**
         * Render a cell.
         * Called by data table for each cell. Override and call with $blnHeader = true if you want
         * this individual cell to render with <<th>> tags instead of <<td>>.
         *
         * @param mixed   $item
         * @param boolean $blnAsHeader
         *
         * @return string
         */
        public function renderCell(mixed $item, ?bool $blnAsHeader = false): string
        {
            if (!$this->blnVisible) {
                return '';
            }

            $cellValue = $this->fetchCellValue($item);
            if ($this->blnHtmlEntities) {
                $cellValue = Q\QString::htmlEntities($cellValue);
            }
            if ($cellValue == '' && Application::instance()->context()->isBrowser(Q\Context::INTERNET_EXPLORER)) {
                $cellValue = '&nbsp;';
            }

            if ($blnAsHeader || $this->blnRenderAsHeader) {
                $strTag = 'th';
            } else {
                $strTag = 'td';
            }

            return Q\Html::renderTag($strTag, $this->getCellParams($item), $cellValue);
        }

        /**
         * Return a key/val array of items to insert inside the cell tag.
         * Handles class, style, and ID already. Override to add additional items, like an onclick handler.
         *
         * @param mixed $item
         *
         * @return array
         */
        protected function getCellParams(mixed $item): array
        {
            $aParams = [];

            if ($strClass = $this->getCellClass($item)) {
                $aParams['class'] = $strClass;
            }

            if ($strId = $this->getCellId($item)) {
                $aParams['id'] = $strId;
            }

            if ($this->blnRenderAsHeader) {
                $aParams['scope'] = 'row';
            }

            // We assume that $this->objCellStyler is always available
            $strStyle = $this->getCellStyle($item);
            $aStyles = $strStyle ? explode(';', $strStyle) : null;

            $aParams = $this->objCellStyler->getHtmlAttributes($aParams, $aStyles);

            // If a callback is specified, we supplement the parameters with it
            if ($this->cellParamsCallback) {
                $a = call_user_func($this->cellParamsCallback, $item);
                $aParams = array_merge($aParams, $a);
            }

            return $aParams;
        }

        /**
         * Return the class of the cell.
         *
         * @param mixed $item
         *
         * @return string|null
         */
        protected function getCellClass(mixed $item): ?string
        {
            if ($this->strCssClass) {
                return $this->strCssClass;
            }
            return '';
        }

        /**
         * Return the ID of the cell.
         *
         * @param mixed $item
         *
         * @return string
         */
        protected function getCellId(mixed $item): string
        {
            return '';
        }

        /**
         * Return the style string for the cell.
         *
         * @param mixed $item
         *
         * @return string
         */
        protected function getCellStyle(mixed $item): string
        {
            return '';
        }

        /**
         * Return the raw string that represents the cell value.
         *
         * @param mixed $item
         */
        abstract public function fetchCellValue(mixed $item): string;

        /**
         * Render the column tag.
         * This special tag can control specific features of columns but is generally optional on a table.
         *
         * @return string
         */
        public function renderColTag(): string
        {
            return Q\Html::renderTag('col', $this->getColParams(), null, true);
        }

        /**
         * Return a key/value array of parameters to put in the col tag.
         * Override to add parameters.
         */
        protected function getColParams(): array
        {
            $aParams = array();
            if ($this->intSpan > 1) {
                $aParams['span'] = $this->intSpan;
            }
            if ($this->strId) {
                $aParams['id'] = $this->strId;
            }

            return $this->objColStyler->getHtmlAttributes($aParams);
        }

        /**
         * Prepare to serialize references to the form.
         */
        public function sleep(): void
        {
            $this->cellParamsCallback = ControlBase::sleepHelper($this->cellParamsCallback);
        }

        /**
         * The object has been unserialized, so fix up pointers to embedded objects.
         * @param FormBase $objForm
         */
        public function wakeup(FormBase $objForm): void
        {
            $this->cellParamsCallback = ControlBase::wakeupHelper($objForm, $this->cellParamsCallback);
        }

        /**
         * Override if necessary to check the data for posts in your column.
         */
        public function parsePostData(): void
        {
        }

        /**
         * PHP magic method
         *
         * @param string $strName
         *
         * @return mixed
         * @throws Caller
         */
        public function __get(string $strName): mixed
        {
            switch ($strName) {
                case 'Name':
                    return $this->strName;
                case 'CssClass':
                    return $this->strCssClass;
                case 'HeaderCssClass':
                    return $this->strHeaderCssClass;
                case 'HtmlEntities':
                    return $this->blnHtmlEntities;
                case 'RenderAsHeader':
                    return $this->blnRenderAsHeader;
                case 'ParentTable':
                    return $this->objParentTable;
                case 'Span':
                    return $this->intSpan;
                case 'Id':
                    return $this->strId;
                case 'Visible':
                    return $this->blnVisible;
                case 'CellStyler':
                    return $this->objCellStyler;
                case 'HeaderCellStyler':
                    return $this->objHeaderCellStyler;
                case 'ColStyler':
                    return $this->objColStyler;

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
         * PHP Magic method
         *
         * @param string $strName
         * @param mixed $mixValue
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function __set(string $strName, mixed $mixValue): void
        {
            switch ($strName) {
                case "Name":
                    try {
                        $this->strName = Type::cast($mixValue, Type::STRING);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                case "CssClass":
                    try {
                        $this->strCssClass = Type::cast($mixValue, Type::STRING);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                case "HeaderCssClass":
                    try {
                        $this->strHeaderCssClass = Type::cast($mixValue, Type::STRING);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                case "HtmlEntities":
                    try {
                        $this->blnHtmlEntities = Type::cast($mixValue, Type::BOOLEAN);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                case "RenderAsHeader":
                    try {
                        $this->blnRenderAsHeader = Type::cast($mixValue, Type::BOOLEAN);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                case "Span":
                    try {
                        $this->intSpan = Type::cast($mixValue, Type::INTEGER);
                        if ($this->intSpan < 1) {
                            throw new Caller("The Span must be 1 or greater.");
                        }
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                case "Id":
                    try {
                        $this->strId = Type::cast($mixValue, Type::STRING);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                case "Visible":
                    try {
                        $this->blnVisible = Type::cast($mixValue, Type::BOOLEAN);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                case "CellParamsCallback":
                    $this->cellParamsCallback = Type::cast($mixValue, Type::CALLABLE_TYPE);
                    break;

                case "_ParentTable":
                    try {
                        $this->objParentTable = Type::cast($mixValue, 'QCubed\Control\TableBase');
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                default:
                    try {
                        parent::__set($strName, $mixValue);
                        break;
                    } catch (Caller $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
            }
        }
    }