<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Jqui;

/**
 * Class MenuBase
 *
 * The MenuBase class defined here provides an interface between the generated
 * MenuGen class, and QCubed. This file is part of the core and will be overwritten
 * when you update QCubed. To override, see the Menu.php file in the controls
 * folder.
 *
 * A JQuery UI menu is designed to turn an unordered html list into a menu widget.
 * In order to create a menu, treat this object as a panel that needs to print
 * <<li>> items inside it. You can create submenus by adding <<ul>> items and putting
 * <<li>> items inside of that.
 *
 * TBD: Add methods to create the menu structure, and to record when a menu is clicked on.
 *
 * @link http://jqueryui.com/menu/
 * @was QMenuBase
 * @package QCubed\Jqui
 */
class MenuBase extends MenuGen
{
    protected $strTagName = 'ul';

}