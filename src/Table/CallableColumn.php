<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Table;

use QCubed\Control\FormBase;
use QCubed\Exception\Caller;
use QCubed\Project\Control\ControlBase;
use QCubed\Type;

/**
 * Class CallableColumn
 *
 * A type of column that lets you use a PHP 'callable'. However, you CANNOT send a PHP closure to this,
 * since closures are not serializable. You CAN do things like array($this, 'method'), or 'Class::StaticMethod'.
 *
 * @property int|string $Index the index or key to use when accessing the arrays in the DataSource array
 * @was QHtmlTableCallableColumn
 * @package QCubed\Table
 */
class CallableColumn extends DataColumn
{
    /** @var callback */
    protected $callback;
    /** @var array extra parameters passed to closure */
    protected $mixParams;

    /**
     * @param string $strName name of the column
     * @param callback $objCallable a callable object. It should take a single argument, the item
     *   of the array. Do NOT pass an actual Closure object, as they are not serializable. However,
     *   you can pass a callable, like array($this, 'method'), or an object that has the __invoke method defined,
     *   as long as its serializable. You can also pass static methods as a string, as in "Class::method"
     * @param mixed $mixParams extra parameters to pass to the closure callback.
     * will be called with the row of the DataSource as that single argument.
     *
     * @throws Caller
     */
    public function __construct($strName, callable $objCallable, $mixParams = null)
    {
        parent::__construct($strName);
        if ($objCallable instanceof \Closure) {
            throw new Caller('\$objCallable Cannot be a Closure.');
        }
        $this->callback = $objCallable;
        $this->mixParams = $mixParams;
    }

    public function fetchCellObject($item)
    {
        if ($this->mixParams) {
            return call_user_func($this->callback, $item, $this->mixParams);
        } else {
            return call_user_func($this->callback, $item);
        }
    }

    /**
     * Fix up possible embedded reference to the form.
     */
    public function sleep()
    {
        $this->callback = ControlBase::sleepHelper($this->callback);
        parent::sleep();
    }

    /**
     * Restore serialized references.
     * @param FormBase $objForm
     */
    public function wakeup(FormBase $objForm)
    {
        parent::wakeup($objForm);
        $this->callback = ControlBase::wakeupHelper($objForm, $this->callback);
    }

    /**
     * PHP magic method
     *
     * @param string $strName
     *
     * @return mixed
     * @throws Caller
     */
    public function __get($strName)
    {
        switch ($strName) {
            case 'Callable':
                return $this->callback;
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
     * PHP magic method
     *
     * @param string $strName
     * @param string $mixValue
     *
     * @return mixed|void
     * @throws Caller
     */
    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            case "Callable":
                $this->callback = Type::cast($mixValue, Type::CALLABLE_TYPE);
                break;

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
