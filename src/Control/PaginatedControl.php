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
use QCubed\Query\QQ;
use QCubed\Type;
use QCubed\Project\Control\Paginator;

/**
 * Class PaginatedControl
 *
 * @property string $Noun Name of the items which are being paginated (book, movie, post etc.)
 * @property string $NounPlural Plural form of name of the items which are being paginated (books, movies, posts etc.)
 * @property PaginatorBase $Paginator
 * @property PaginatorBase $PaginatorAlternate
 * @property boolean $UseAjax
 * @property integer $ItemsPerPage   is how many items you want to display per page when Pagination is enabled
 * @property integer $TotalItemCount is the total number of items in the ENTIRE recordset -- only used when Pagination is enabled
 * @property mixed $DataSource     is an array of anything.  THIS MUST BE SET EVERY TIME (DataSource does NOT persist from postback to postback
 * @property-read mixed $LimitClause
 * @property-read mixed $LimitInfo      is what should be passed in to the LIMIT clause of the sql query that retrieves the array of items from the database
 * @property-read integer $ItemCount
 * @property integer $PageNumber     is the current page number you are viewing
 * @property-read integer $PageCount
 * @property-read integer $ItemsOffset    Current offset of Items from the result
 * @was QPaginatedControl
 * @package QCubed\Control
 */
abstract class PaginatedControl extends Q\Project\Control\ControlBase
{
    use DataBinderTrait;

    // APPEARANCE
    /** @var string Name of the items which are being paginated (books, movies, posts etc.) */
    protected $strNoun;
    /**  @var string Plural form of name of the items which are being paginated (books, movies, posts etc.) */
    protected $strNounPlural;

    // BEHAVIOR
    /** @var null|Paginator Paginator at the top */
    protected $objPaginator = null;
    /** @var null|Paginator Paginator at the bottom */
    protected $objPaginatorAlternate = null;
    /** @var bool Determines whether this QDataGrid wll use AJAX or not */
    protected $blnUseAjax = true;

    // MISC
    /** @var array DataSource from which the items are picked and rendered */
    protected $objDataSource;

    // SETUP
    /** @var bool Is this paginator a block element? */
    protected $blnIsBlockElement = true;

    /**
     * PaginatedControl constructor.
     * @param ControlBase|FormBase $objParentObject
     * @param null|string $strControlId
     */
    public function __construct($objParentObject, $strControlId = null)
    {
        parent::__construct($objParentObject, $strControlId);

        $this->strNoun = t('item');
        $this->strNounPlural = t('items');
    }

    /**
     * @return bool
     */
    public function validate()
    {
        return true;
    }

    /**
     * @throws Caller
     */
    public function dataBind()
    {
        // Run the DataBinder (if applicable)
        if (($this->objDataSource === null) && ($this->hasDataBinder()) && (!$this->blnRendered)) {
            try {
                $this->callDataBinder();
            } catch (Caller $objExc) {
                $objExc->incrementOffset();
                throw $objExc;
            }

            if ($this->objPaginator && $this->PageNumber > $this->PageCount) {
                $this->PageNumber = max($this->PageCount, 1);
            }
        }
    }

    /**
     * PHP magic method
     * @param string $strName Property name
     *
     * @return mixed
     * @throws Caller
     */
    public function __get($strName)
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
                if ($this->objPaginator) {
                    return $this->objPaginator->ItemsPerPage;
                } else {
                    return null;
                }
            case "ItemsOffset":
                if ($this->objPaginator) {
                    return ($this->objPaginator->PageNumber - 1) * $this->objPaginator->ItemsPerPage;
                } else {
                    return null;
                }
            case "TotalItemCount":
                if ($this->objPaginator) {
                    return $this->objPaginator->TotalItemCount;
                } else {
                    return null;
                }

            // MISC
            case "DataSource":
                return $this->objDataSource;
            case "LimitClause":
                if ($this->objPaginator) {
                    //						if ($this->objPaginator->TotalItemCount > 0) {
                    $intOffset = $this->ItemsOffset;
                    return QQ::limitInfo($this->objPaginator->ItemsPerPage, $intOffset);
//						}
                }
                return null;
            case "LimitInfo":
                if ($this->objPaginator) {
                    //						if ($this->objPaginator->TotalItemCount > 0) {
                    $intOffset = $this->ItemsOffset;
                    return $intOffset . ',' . $this->objPaginator->ItemsPerPage;
//						}
                }
                return null;
            case "ItemCount":
                return count($this->objDataSource);

            case 'PageNumber':
                if ($this->objPaginator) {
                    return $this->objPaginator->PageNumber;
                } else {
                    return null;
                }

            case 'PageCount':
                if ($this->objPaginator) {
                    return $this->objPaginator->PageCount;
                } else {
                    return null;
                }

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
     * @param string $strName
     * @param string $mixValue
     * @throws Caller
     * @throws InvalidCast
     * @return void
     */
    public function __set($strName, $mixValue)
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
