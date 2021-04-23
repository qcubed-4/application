<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed;

use QCubed as Q;
use QCubed\Exception\Caller;
use QCubed\Project\Application;

/**
 * Class JsResponse
 *
 * This class is an internal class for use by the application object. It manages the response to requests from the client,
 * storing the various responses from the framework, and ultimately returning code that is either displayable in an
 * html page (for Server requests), or that is a json response to an Ajax query that will get unpacked and executed in qcubed.js
 *
 * @package QCubed
 */
class JsResponse
{

    /* JS Response entries that qcubed.js will handle */
    const WATCHER = 'watcher';
    const CONTROLS = 'controls';
    const COMMANDS_HIGH = 'commandsHigh';
    const COMMANDS_MEDIUM = 'commands';
    const COMMANDS_LOW = 'commandsLow';
    const COMMANDS_FINAL = 'commandsFinal';    // execute after all ajax commands, and resultant ajax commands have executed
    const REG_C = 'regc'; // register control list
    const HTML = 'html';
    const VALUE = 'value';
    const ID = 'id';
    const ATTRIBUTES = 'attributes';
    const CSS = 'css';
    const CLOSE = 'winclose';
    const LOCATION = 'loc';
    const ALERT = 'alert';
    const STYLE_SHEETS = 'ss';
    const JAVA_SCRIPTS = 'js';

    /**
     * If this particular item is set, we ensure that this command, and only this command will get invoked on the
     * next response. The rest of the commands will wait until the next response.
     *
     * @var null|array;
     */
    protected $exclusiveCommand = null;

    /** @var array A structured array of commands to be sent to either the ajax response, or page output.
     * Replaces the AlertMessageArray, JavaScriptArray, JavaScriptArrayHighPriority, and JavaScriptArrayLowPriority.
     */
    protected $commands = array();

    /** @var array JS files to be added to the list of files in front of the javascript commands. Should include jquery, etc. */
    protected $files = array();


    /**
     * Causes the browser to display a JavaScript alert() box with supplied message
     * @param string $strMessage Message to be displayed
     */
    public function displayAlert($strMessage)
    {
        $this->commands[self::ALERT][] = $strMessage;
    }

    /**
     * This class can be used to call a Javascript function in the client browser from the server side.
     * Can be used inside event handlers to do something after verification  on server side.
     *
     * TODO: Since this is implemented with an "eval" on the client side in ajax, we should phase this out in favor
     * of specific commands sent to the client.
     *
     * @static
     * @deprecated Will be eventually removed. If you need to do something in javascript, add it to AjaxResponse.
     * @param string $strJavaScript the javascript to execute
     * @param string $strPriority
     * @throws Caller
     */
    public function executeJavaScript($strJavaScript, $strPriority = ApplicationBase::PRIORITY_STANDARD)
    {
        if (is_bool($strPriority)) {
            //we keep this codepath for backward compatibility
            if ($strPriority === true) {
                throw new Caller('Please specify a correct priority value');
            }
        } else {
            switch ($strPriority) {
                case ApplicationBase::PRIORITY_HIGH:
                    $this->commands[self::COMMANDS_HIGH][] = ['script' => $strJavaScript];
                    break;
                case ApplicationBase::PRIORITY_LOW:
                    $this->commands[self::COMMANDS_LOW][] = ['script' => $strJavaScript];
                    break;
                case ApplicationBase::PRIORITY_EXCLUSIVE:
                    $this->exclusiveCommand = ['script' => $strJavaScript];
                    break;
                default:
                    $this->commands[self::COMMANDS_MEDIUM][] = ['script' => $strJavaScript];
                    break;
            }
        }
    }

    /**
     * Execute a function on a particular control. Many javascript widgets are structured this way, and this gives us
     * a general purpose way of sending commands to widgets without an 'eval' on the client side.
     *
     * Commands will be executed in the order received, along with ExecuteJavaScript commands and ExecuteObjectCommands.
     * If you want to force a command to execute first, give it high priority, or last, give it low priority.
     *
     * @param string $strControlId Id of control to direct the command to.
     * @param string $strFunctionName Function name to call. For jQueryUI, this would be the widget name
     * @param string $strFunctionName,... Unlimited OPTIONAL parameters to use as a parameter list to the function. List can
     *                                        end with a PRIORITY_* to prioritize the command.
     */
    public function executeControlCommand($strControlId, $strFunctionName /*, ..., PRIORITY_* */)
    {
        $args = func_get_args();
        $args[0] = '#' . $strControlId;
        call_user_func_array([$this, 'executeSelectorFunction'], $args);
    }

    /**
     * Call a function on a jQuery selector. The selector can be a single string, or an array where the first
     * item is a selector specifying the items within the context of the second selector.
     *
     * @param array|string $mixSelector
     * @param string $strFunctionName
     * @param string $strFunctionName,... Unlimited OPTIONAL parameters to use as a parameter list to the function. List can
     *                                        end with a PRIORITY_* to prioritize the command.
     * @throws Caller
     */
    public function executeSelectorFunction($mixSelector, $strFunctionName /*, ..., PRIORITY_* */)
    {
        if (!(is_string($mixSelector) || (is_array($mixSelector) && count($mixSelector) == 2))) {
            throw new Caller('Selector must be a string or an array of two items');
        }
        $args = func_get_args();
        array_shift($args);
        array_shift($args);
        if ($args && end($args) === ApplicationBase::PRIORITY_HIGH) {
            $code = self::COMMANDS_HIGH;
            array_pop($args);
        } elseif ($args && end($args) === ApplicationBase::PRIORITY_LOW) {
            $code = self::COMMANDS_LOW;
            array_pop($args);
        } elseif ($args && end($args) === ApplicationBase::PRIORITY_EXCLUSIVE) {
            array_pop($args);
            $this->exclusiveCommand = [
                'selector' => $mixSelector,
                'func' => $strFunctionName,
                'params' => $args
            ];
            return;
        } elseif ($args && end($args) === ApplicationBase::PRIORITY_LAST) {
            array_pop($args);
            $this->commands[self::COMMANDS_FINAL][] = [
                'selector' => $mixSelector,
                'func' => $strFunctionName,
                'params' => $args,
                'final' => true
            ];
            return;
        } else {
            $code = self::COMMANDS_MEDIUM;
        }
        if (empty($args)) {
            $args = null;
        }

        $this->commands[$code][] = [
            'selector' => $mixSelector,
            'func' => $strFunctionName,
            'params' => $args
        ];
    }


    /**
     * Call the given function with the given arguments. If just a function name, then the window object is searched.
     * The function can be inside an object accessible from the global namespace by separating with periods.
     * @param string $strFunctionName Can be namespaced, as in "qcubed.func".
     * @param string $strFunctionName,... Unlimited OPTIONAL parameters to use as a parameter list to the function. List can
     *                                        end with a PRIORITY_* to prioritize the command.
     */
    public function executeJsFunction($strFunctionName /*, ... */)
    {
        $args = func_get_args();
        array_shift($args);
        if ($args && end($args) === ApplicationBase::PRIORITY_HIGH) {
            $code = self::COMMANDS_HIGH;
            array_pop($args);
        } elseif ($args && end($args) === ApplicationBase::PRIORITY_LOW) {
            $code = self::COMMANDS_LOW;
            array_pop($args);
        } elseif ($args && end($args) === ApplicationBase::PRIORITY_EXCLUSIVE) {
            array_pop($args);
            $this->exclusiveCommand = ['func' => $strFunctionName, 'params' => $args];
            return;
        } else {
            $code = self::COMMANDS_MEDIUM;
        }
        if (empty($args)) {
            $args = null;
        }

        $this->commands[$code][] = ['func' => $strFunctionName, 'params' => $args];
    }

    /**
     * One time add of style sheets, to be used by QForm only for last minute style sheet injection.
     * @param string[] $strStyleSheetArray
     */
    public function addStyleSheets(array $strStyleSheetArray)
    {
        if (empty($this->commands[self::STYLE_SHEETS])) {
            $this->commands[self::STYLE_SHEETS] = $strStyleSheetArray;
        } else {
            $this->commands[self::STYLE_SHEETS] =
                array_merge($this->commands[self::STYLE_SHEETS], $strStyleSheetArray);
        }
    }

    /**
     * Add an array of javascript files for one-time inclusion. Called by QForm. Do not call.
     * @param string[] $strJavaScriptFileArray
     */
    public function addJavaScriptFiles($strJavaScriptFileArray)
    {
        if (empty($this->files[self::JAVA_SCRIPTS])) {
            $this->files[self::JAVA_SCRIPTS] = $strJavaScriptFileArray;
        } else {
            $this->files[self::JAVA_SCRIPTS] =
                array_merge($this->files[self::JAVA_SCRIPTS], $strJavaScriptFileArray);
        }
    }

    /**
     * Function renders all the Javascript commands as output to the client browser. This is a mirror of what
     * occurs in the success function in the qcubed.js ajax code.
     *
     * @param bool $blnBeforeControls True to only render the javascripts that need to come before the controls are defined.
     *                                This is used to break the commands issued into two groups.
     * @static
     * @return string
     */
    public function renderJavascript($blnBeforeControls = false)
    {
        $strScript = '';

        // Style sheet injection by a control. Not very common, as other ways of adding style sheets would normally be done first.
        if (!empty($this->commands[self::STYLE_SHEETS])) {
            $str = '';
            foreach ($this->commands[self::STYLE_SHEETS] as $ss) {
                $str .= 'qc.loadStyleSheetFile("' . $ss . '", "all"); ';
            }
            $this->commands[self::STYLE_SHEETS] = null;
        }

        if (!empty($this->commands[self::ALERT])) {
            foreach ($this->commands[self::ALERT] as $strAlert) {
                $strAlert = json_encode($strAlert);
                $strScript .= sprintf('alert(%s); ', $strAlert);
            }
            $this->commands[self::ALERT] = null;
        }

        if (!empty($this->commands[self::COMMANDS_HIGH])) {
            $strScript .= self::renderCommandArray($this->commands[self::COMMANDS_HIGH]);
            $this->commands[self::COMMANDS_HIGH] = null;
        }

        if ($blnBeforeControls) {
            return $strScript;
        }    // When we call again, everything above here will be skipped since we are emptying the arrays

        if (!empty($this->commands[self::COMMANDS_MEDIUM])) {
            $strScript .= self::renderCommandArray($this->commands[self::COMMANDS_MEDIUM]);
            $this->commands[self::COMMANDS_MEDIUM] = null;
        }

        if (!empty($this->commands[self::COMMANDS_LOW])) {
            $strScript .= self::renderCommandArray($this->commands[self::COMMANDS_LOW]);
        }

        // A Application::redirect
        if (!empty($this->commands[self::LOCATION])) {
            $strLocation = $this->commands[self::LOCATION];
            $strScript .= sprintf('document.location = "%s";', $strLocation);
        }
        if (!empty($this->commands[self::CLOSE])) {
            $strScript .= 'window.close();';
        }

        $this->commands = array();

        return $strScript;
    }

    /**
     * @param array $commandArray
     * @return string
     */
    private function renderCommandArray(array $commandArray)
    {
        $strScript = '';
        foreach ($commandArray as $command) {
            if (isset($command['script'])) {    // a script to use eval on
                $strScript .= sprintf('%s;', $command['script']) . _nl();
            } elseif (isset($command['selector'])) {    // a control function
                if (is_array($command['selector'])) {
                    $strSelector = sprintf('"%s", "%s"', $command['selector'][0], $command['selector'][1]);
                } else {
                    $strSelector = '"' . $command['selector'] . '"';
                }

                if ($params = $command['params']) {
                    $objParams = new Q\Js\ParameterList($params);
                    $strParams = $objParams->toJsObject();
                } else {
                    $strParams = '';
                }
                $strScript .= sprintf('jQuery(%s).%s(%s);', $strSelector, $command['func'], $strParams) . _nl();
            } elseif (isset($command['func'])) {    // a function call
                if ($params = $command['params']) {
                    $objParams = new Q\Js\ParameterList($params);
                    $strParams = $objParams->toJsObject();
                } else {
                    $strParams = '';
                }
                $strScript .= sprintf('%s(%s);', $command['func'], $strParams) . _nl();
            }
        }
        return $strScript;
    }

    /**
     * Return the javascript command array, for use by form ajax response. Will erase the command array, so
     * the form better use it.
     * @static
     * @return array
     */
    public function getJavascriptCommandArray()
    {
        if ($this->exclusiveCommand) {
            // only render this one;
            $a[self::COMMANDS_MEDIUM] = [$this->exclusiveCommand];
            $this->exclusiveCommand = null;
            return $a;
        }

        // Combine the javascripts into one array item
        $scripts = array();
        if (!empty($this->commands[self::COMMANDS_MEDIUM])) {
            $scripts = $this->commands[self::COMMANDS_MEDIUM];
        }
        if (!empty($this->commands[self::COMMANDS_HIGH])) {
            $scripts = array_merge($this->commands[self::COMMANDS_HIGH], $scripts);
            unset($this->commands[self::COMMANDS_HIGH]);
        }
        if (!empty($this->commands[self::COMMANDS_LOW])) {
            $scripts = array_merge($scripts, $this->commands[self::COMMANDS_LOW]);
            unset($this->commands[self::COMMANDS_LOW]);
        }
        if (!empty($this->commands[self::COMMANDS_FINAL])) {
            $scripts = array_merge($scripts, $this->commands[self::COMMANDS_FINAL]);
            unset($this->commands[self::COMMANDS_FINAL]);
        }
        if ($scripts) {
            $this->commands[self::COMMANDS_MEDIUM] = $scripts;
        }

        // add the file inclusion array onto the front of the command array
        $a = array_merge($this->files, $this->commands);
        $this->files = array();
        $this->commands = array();
        return $a;
    }

    /**
     * Render scripts for injecting files into the html output. This is for server only, not ajax.
     * This list will appear ahead of the javascript commands rendered below.
     *
     * @static
     * @return string
     */
    public function renderFiles()
    {
        $strScript = '';

        // Javascript files should get processed before the commands.
        if (!empty($this->files[self::JAVA_SCRIPTS])) {
            foreach ($this->files[self::JAVA_SCRIPTS] as $js) {
                $strScript .= '<script type="text/javascript" src="' . Application::getJsFileUri($js) . '"></script>' . "\n";
            }
        }

        $this->files = array();

        return $strScript;
    }

    /**
     * @param $strLocation
     */
    public function setLocation($strLocation)
    {
        $this->commands[self::LOCATION] = $strLocation;
    }

    /**
     * Closes the window through a qcubed.js command
     */
    public function closeWindow()
    {
        $this->commands[self::CLOSE] = true;
    }

    public function hasExclusiveCommand() {
        return (!empty($this->exclusiveCommand));
    }

}
