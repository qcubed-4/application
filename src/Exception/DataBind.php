<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Exception;

use QCubed as Q;

/**
 * Class DataBind
 *
 * @property-read integer $Offset
 * @property-read mixed $BackTrace
 * @property-read string $Query
 * @was QDataBindException
 * @package QCubed\Exception
 */
class DataBind extends Caller
{
    private $intOffset;
    private $strTraceArray;
    private $strQuery;

    /**
     * DataBind constructor.
     * @param Caller $objExc
     */
    public function __construct(Caller $objExc)
    {
        parent::__construct($objExc->getMessage(), $objExc->getCode());
        $this->intOffset = $objExc->Offset;
        $this->strTraceArray = $objExc->TraceArray;

        if ($objExc instanceof Q\Database\Exception\ExceptionBase) {
            $this->strQuery = $objExc->Query;
        }

    }

    /**
     * @param $strName
     * @return mixed
     */
    public function __get($strName)
    {
        switch ($strName) {
            case "Offset":
                return $this->intOffset;

            case "BackTrace":
                $objTraceArray = debug_backtrace();
                return (var_export($objTraceArray, true));

            case "Query":
                return $this->strQuery;
        }

        return null;
    }
}