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

    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Project\Control\ControlBase;
    use QCubed\Project\Control\Paginator;
    use QCubed\Query\QQ;
    use QCubed\Type;
    use Throwable;

    /**
     * Class PaginatedControl
     *
     * @property string $Noun Name of the items which are being paginated (book, movie, post, etc.)
     * @property string $NounPlural Plural form of name of the items which are being paginated (books, movies, posts, etc.)
     * @property PaginatorBase $Paginator
     * @property PaginatorBase $PaginatorAlternate
     * @property boolean $UseAjax
     * @property integer $ItemsPerPage indicates how many items you want to display on a single page if pages are enabled.
     * @property integer $TotalItemCount is the total number of items in the ENTIRE recordset -- only used when Pagination is enabled?
     * @property mixed $DataSource     is an array of anything.  THIS MUST BE SET EVERY TIME (DataSource does NOT persist from postback to postback
     * @property-read mixed $LimitClause
     * @property-read mixed $LimitInfo If what should be passed in to the LIMIT clause of the SQL query that retrieves the array of items from the database
     * @property-read integer $ItemCount
     * @property integer $PageNumber     is the current page number you are viewing
     * @property-read integer $PageCount
     * @property-read integer $ItemsOffset    Current offset of Items from the result
     *
     * @package QCubed\Control
     */
    abstract class PaginatedControl extends ControlBase
    {
        use DataBinderTrait;

        // APPEARANCE
        /** @var string Name of the items which are being paginated (books, movies, posts, etc.) */
        protected string $strNoun;
        /**  @var string Plural form of name of the items which are being paginated (books, movies, posts, etc.) */
        protected string $strNounPlural;

        // BEHAVIOR
        /** @var null|Paginator Paginator at the top */
        protected ?Paginator $objPaginator = null;
        /** @var null|Paginator Paginator at the bottom */
        protected ?Paginator $objPaginatorAlternate = null;
        /** @var bool Determines whether this QDataGrid wll uses AJAX or not */
        protected bool $blnUseAjax = true;

        // MISC
        /** @var array DataSource, from which the items are picked and rendered */
        protected array $objDataSource = [];

        // SETUP
        /** @var bool Is this paginator a block element? */
        protected bool $blnIsBlockElement = true;

        /**
         * PaginatedControl constructor.
         * @param ControlBase|FormBase $objParentObject
         * @param string|null $strControlId
         * @throws Caller
         */
        public function __construct(FormBase|ControlBase $objParentObject, ?string $strControlId = null)
        {
            parent::__construct($objParentObject, $strControlId);

            $this->strNoun = t('item');
            $this->strNounPlural = t('items');
        }

        /**
         * @return bool
         */
        public function validate(): bool
        {
            return true;
        }

        /**
         * @throws Caller
         */
        public function dataBind(): void
        {
            // Run the DataBinder (if applicable)
            if ($this->objDataSource !== [] || !$this->hasDataBinder()) {
                return;
            }

            if ($this->blnRendered) {
                return;
            }

            try {
                $this->callDataBinder();
            } catch (Caller $objExc) {
                $objExc->incrementOffset();
                throw $objExc;
            }

            if (isset($this->objPaginator) &&  $this->PageNumber > $this->PageCount) {
                $this->PageNumber = max($this->PageCount, 1);
            }

        }

        /**
         * Magic getter method to retrieve the value of a property based on its name.
         *
         * @param string $strName The name of the property being accessed.
         *
         * @return mixed The value of the requested property, or the result of the parent __get method if applicable.
         * @throws Caller If the property does not exist or cannot be accessed.
         * @throws \Exception
         */
        public function __get(string $strName): mixed
        {
            switch ($strName) {
                // APPEARANCE
                case "Noun":
                    return $this->strNoun;

                case "NounPlural":
                    return $this->strNounPlural;

                // BEHAVIOR
                case "Paginator":
                    return $this->objPaginator;

                case "PaginatorAlternate":
                    return $this->objPaginatorAlternate;

                case "UseAjax":
                    return $this->blnUseAjax;

                case "ItemsPerPage":
                    return $this->objPaginator?->ItemsPerPage;

                case "ItemsOffset":
                    if ($this->objPaginator) {
                        return ($this->objPaginator->PageNumber - 1) * $this->objPaginator->ItemsPerPage;
                    } else {
                        return '';
                    }

                case "TotalItemCount":
                    return $this->objPaginator?->TotalItemCount;

                // MISC
                case "DataSource":
                    return $this->objDataSource;

                case "LimitClause":
                    if ($this->objPaginator) {
                        if ($this->objPaginator->TotalItemCount > 0) {
                            $intOffset = $this->ItemsOffset;
                            return QQ::limitInfo($this->objPaginator->ItemsPerPage, $intOffset);
                        }
                    }
                    break;

                case "LimitInfo":
                    if ($this->objPaginator) {
                        if ($this->objPaginator->TotalItemCount > 0) {
                            $intOffset = $this->ItemsOffset;
                            return $intOffset . ',' . $this->objPaginator->ItemsPerPage;
                        }
                    }
                    break;
                case "ItemCount":
                    return count($this->objDataSource);

                case 'PageNumber':
                    return $this->objPaginator?->PageNumber;

                case 'PageCount':
                    return $this->objPaginator?->PageCount;

                default:
                    try {
                        return parent::__get($strName);
                    } catch (Caller $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
            }
            // Fallback return statement to satisfy the return type, could be adjusted as needed
            return '';
        }

        /**
         * Magic method to set the value of a property.
         *
         * @param string $strName The name of the property to set.
         * @param mixed $mixValue The value to assign to the property.
         * @return void
         * @throws Caller Thrown if a property-specific validation or assignment fails.
         * @throws InvalidCast Thrown if the value cannot be cast to the expected type.
         * @throws Throwable
         */
        public function __set(string $strName, mixed $mixValue): void
        {
            switch ($strName) {
                // APPEARANCE
                case "Noun":
                    try {
                        $this->strNoun = Type::cast($mixValue, Type::STRING);
                        $this->blnModified = true;
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                case "NounPlural":
                    try {
                        $this->strNounPlural = Type::cast($mixValue, Type::STRING);
                        $this->blnModified = true;
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                // BEHAVIOR
                case "Paginator":
                    try {
                        $this->objPaginator = Type::cast($mixValue, '\\QCubed\\Control\\PaginatorBase');
                        if ($this->objPaginator) {
                            if ($this->objPaginator->Form->FormId != $this->Form->FormId) {
                                throw new Caller('The assigned paginator must belong to the same form that this control belongs to.');
                            }
                            $this->objPaginator->setPaginatedControl($this);
                        }
                        $this->blnModified = true;
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                case "PaginatorAlternate":
                    try {
                        $this->objPaginatorAlternate = Type::cast($mixValue, '\\QCubed\\Control\\PaginatorBase');
                        if ($this->objPaginatorAlternate->Form->FormId != $this->Form->FormId) {
                            throw new Caller('The assigned paginator must belong to the same form that this control belongs to.');
                        }
                        $this->objPaginatorAlternate->setPaginatedControl($this);
                        $this->blnModified = true;
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                case "UseAjax":
                    try {
                        $this->blnUseAjax = Type::cast($mixValue, Type::BOOLEAN);

                        if ($this->objPaginator) {
                            $this->objPaginator->UseAjax = $this->blnUseAjax;
                        }
                        if ($this->objPaginatorAlternate) {
                            $this->objPaginatorAlternate->UseAjax = $this->blnUseAjax;
                        }

                        $this->blnModified = true;
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                case "ItemsPerPage":
                    if ($this->objPaginator) {
                        try {
                            $intItemsPerPage = Type::cast($mixValue, Type::INTEGER);
                            $this->objPaginator->ItemsPerPage = $intItemsPerPage;

                            if ($this->objPaginatorAlternate) {
                                $this->objPaginatorAlternate->ItemsPerPage = $intItemsPerPage;
                            }

                            $this->blnModified = true;
                            break;
                        } catch (Caller $objExc) {
                            $objExc->incrementOffset();
                            throw $objExc;
                        }
                    } else {
                        throw new Caller('Setting ItemsPerPage requires a Paginator to be set');
                    }

                case "TotalItemCount":
                    if ($this->objPaginator) {
                        try {
                            $intTotalCount = Type::cast($mixValue, Type::INTEGER);
                            $this->objPaginator->TotalItemCount = $intTotalCount;

                            if ($this->objPaginatorAlternate) {
                                $this->objPaginatorAlternate->TotalItemCount = $intTotalCount;
                            }

                            $this->blnModified = true;
                            break;
                        } catch (Caller $objExc) {
                            $objExc->incrementOffset();
                            throw $objExc;
                        }
                    } else {
                        throw new Caller('Setting TotalItemCount requires a Paginator to be set');
                    }

                // MISC
                case "DataSource":
                    $this->objDataSource = $mixValue;
                    $this->blnModified = true;
                    break;

                case "PageNumber":
                    if ($this->objPaginator) {
                        try {
                            $intPageNumber = Type::cast($mixValue, Type::INTEGER);
                            $this->objPaginator->PageNumber = $intPageNumber;

                            if ($this->objPaginatorAlternate) {
                                $this->objPaginatorAlternate->PageNumber = $intPageNumber;
                            }
                            $this->blnModified = true;
                            break;
                        } catch (Caller $objExc) {
                            $objExc->incrementOffset();
                            throw $objExc;
                        }
                    } else {
                        throw new Caller('Setting PageNumber requires a Paginator to be set');
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
