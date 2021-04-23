<?php

/**
 * This script generates the Jq*Gen classes by scraping the JQueryUI documentation web site.
 * Current version: JQueryUI 1.12
 */

require('jq_control.php');
require('qcubed.inc.php');

class HtmlJqDoc extends JqDoc {

	public function description($desc_node) {
		$description = '';
		while ($desc_node) {
			if (strpos($desc_node->plaintext, 'Code examples:') !== false) {
				break;
			}
//			if ($description)
//				$description .= "\n";
			$text = $desc_node->outertext();
			$text = preg_replace('/<(\w+)[^>]*>\s*/', '<$1>', $text);
			$text = preg_replace('/\s*<\/(\w+)>/', '</$1>', $text);
			$text = preg_replace('/<\/code>\s*<code>/', '', $text);
			$text = preg_replace('/<div>/', '', $text);
			$text = preg_replace('/<\/div>/', '', $text);

			$text = preg_replace('/<strong>/', '', $text);
			$text = preg_replace('/<\/strong>/', '', $text);

			$description .= $text;
			$desc_node = $desc_node->next_sibling();
		}
		return $description;
	}

	public function __construct($strUrl, $strJqClass, $strJqSetupFunc, $strQcClass, $strQcBaseClass)
	{
		$this->hasDisabledProperty = false;
		$html = file_get_html($strUrl);

		if ($strJqClass === null) {
			$nodes = $html->find('h1.entry-title');
			$strJqClass = preg_replace('/ .*/', '', $nodes[0]->plaintext);
		}

		if ($strQcClass) {
            if ($strQcClass == 'Button') {
                $strOldClass = 'QJqButton';
            } elseif ($strQcClass == 'Checkbox') {
                $strOldClass = 'QJqCheckBox';
            } elseif ($strQcClass == 'RadioButton') {
                $strOldClass = 'QJqRadioButton';
            }
            else {
                $strOldClass = 'Q' . $strQcClass;
            }
        } else {
            $strOldClass = 'Q' . $strJqClass;
        }



        parent::__construct($strOldClass, $strJqClass, $strJqSetupFunc, $strQcClass, $strQcBaseClass);

		$htmlOptions = $html->find('section[id=options] div.api-item');

		foreach ($htmlOptions as $htmlOption) {
			$type = $this->add_option($htmlOption);
			/*
			if ($this->is_event_option($type)) {
				$this->add_event($htmlOption, $type);
			}*/
		}

		$htmlEvents = $html->find('section[id=events] div.api-item');
		foreach ($htmlEvents as $htmlEvent) {
			$this->add_event($htmlEvent);
		}

		$htmlMethods = $html->find('section[id=methods] div.api-item');
		$this->reset_names();
		foreach ($htmlMethods as $htmlMethod) {
			$this->add_method($htmlMethod);
		}
	}

	public function add_option($htmlOption) {
		$nodes = $htmlOption->find('h3');
		$name_node = $nodes[0];
		$origName = $name = preg_replace('/\W.*/', '', $name_node->innertext());

		$nodes = $htmlOption->find('div.option-type');
		$type = preg_replace('/Type: /', '', $nodes[0]->plaintext);
		$type = trim($type);
		/*
		if ($this->is_event_option($type))
			return $type;
*/

		// sometimes jQuery controls (e.g. tabs) uses the same property name for more than one options
		$name = $this->unique_name($name);

		$defaultValue = null;
		$nodes = $htmlOption->find('div.default');
		if ($nodes) {
			$desc_node = $nodes[0]->next_sibling();
			$nodes = $nodes[0]->find('code');
			$defaultValue = html_entity_decode($nodes[0]->plaintext, ENT_COMPAT, 'UTF-8');
		} else {
			$desc_node = $name_node->next_sibling();
		}
		$description = $this->description($desc_node);
		if ($name == 'disabled') {
			$this->hasDisabledProperty = true;
		}

		$this->options[] = new Option($name, $origName, $type, $defaultValue, $description);
		return $type;
	}
/**  This was always wrong. Options with function arguments do not generate events. You must pass a javascript function to them to use them.
 *
	public function is_event_option($type) {
		return stripos($type, 'function') !== false && strpos($type, ' or ') === false;
	}
*/
	public function add_event($htmlEvent, $type = null) {
		$nodes = $htmlEvent->find('h3');
		$name_node = $nodes[0];
		$origName = $name = preg_replace('/\W.*/', '', $name_node->innertext());
		if (substr($name, 0, 2) !== "on") {
			$name = "on" . ucfirst($name);
		}

		if ($type == null) {
			$nodes = $htmlEvent->find('span.returns');
			$type = preg_replace('/Type: /', '', $nodes[0]->plaintext);
		}
		$type = trim($type);

		// sometimes jQuery controls (e.g. tabs) uses the same property name for more than one options
		$name = $this->unique_name($name);

		$desc_node = $name_node->next_sibling();
		$description = $this->description($desc_node);

		if (stripos($type, 'function') === 0) { // this can only be declared at init time
			$this->options[] = new Event($this->strOldClass, $this->strQcClass, $name, $origName, $type, $description);
		} else {
			$this->events[] = new Event($this->strOldClass, $this->strQcClass, $name, $origName, $type, $description);
		}
	}

	public function add_method($htmlMethod) {
		$nodes = $htmlMethod->find('h3');
		$name_node = $nodes[0];
		$origName = $name = preg_replace('/\W.*/', '', $name_node->innertext());
		if ($origName === "widget") {
			// the widget method doesn't make much sense in our context
			// skip it
			return;
		}

		$signature = preg_replace('/\).*/', ')', $name_node->innertext());
		$signature = str_replace('[,', ',[', $signature);

        /**
         * Make sure name of method does not conflict with a control's method name.
         */
        if ($name == 'focus') {
            $name = $name . '1';
        }

		// sometimes jQuery controls (e.g. tabs) uses the same property name for more than one options
		$name = $this->unique_name($name);

		$desc_node = $name_node->next_sibling();
		$description = $this->description($desc_node);

		$this->methods[] = new Method($name, $origName, $signature, $description);
	}
}

$aryPathsList  = array();

function CamelCaseFromDash($strName) {
	$strToReturn = '';

	// If entire underscore string is all uppercase, force to all lowercase
	// (mixed case and all lowercase can remain as is)
	if ($strName == strtoupper($strName))
		$strName = strtolower($strName);

	while (($intPosition = strpos($strName, "-")) !== false) {
		// Use 'ucfirst' to create camelcasing
		$strName = ucfirst($strName);
		if ($intPosition == 0) {
			$strName = substr($strName, 1);
		} else {
			$strToReturn .= substr($strName, 0, $intPosition);
			$strName = substr($strName, $intPosition + 1);
		}
	}

	$strToReturn .= ucfirst($strName);
	return $strToReturn;
}


function jq_control_gen($strUrl, $strQcClass = null, $strQcBaseClass = 'QCubed\\Control\\Panel') {
	global $aryPathsList;

	$strAppDir = dirname(dirname(__DIR__));
	$strOutDirControls = $strAppDir . "/install/project/qcubed/Jqui";
	$strOutDirControlsBase = $strAppDir . "/src/Jqui";

	$jqControlGen = new JqControlGen();
	$objJqDoc = new HtmlJqDoc($strUrl, null, null, $strQcClass, $strQcBaseClass);
	$jqControlGen->GenerateControl($objJqDoc, $strOutDirControls, $strOutDirControlsBase);
		
}

// Load up the autoloader
$dirQCubed = dirname(dirname(dirname(__DIR__)));
$dirVendor = dirname($dirQCubed);
$dirProject = dirname($dirVendor) . '/project';

$loader = require $dirVendor . '/autoload.php'; // load composer autoloader
$loader->addPsr4('QCubed\\', $dirQCubed . '/common/src'); // make sure common is included
$loader->addPsr4('QCubed\\', $dirQCubed . '/application/src'); // make sure application is included

//$loader->addPsr4('QCubed\\Project\\', $dirProject . '/qcubed'); // make sure project is included


$baseUrl = "http://api.jqueryui.com";

// QBlock control uses these differently to make these capabilities a part of any block control

jq_control_gen($baseUrl."/draggable", null, 'QCubed\\Project\\Control\\ControlBase');
jq_control_gen($baseUrl."/droppable", null, 'QCubed\\Project\\Control\\ControlBase');
jq_control_gen($baseUrl."/resizable",  null, 'QCubed\\Project\\Control\\ControlBase');

jq_control_gen($baseUrl."/selectable");
jq_control_gen($baseUrl."/sortable");

jq_control_gen($baseUrl."/accordion");
jq_control_gen($baseUrl."/autocomplete", null, 'QCubed\\Project\Control\\TextBox');
jq_control_gen($baseUrl."/button", 'Button', 'QCubed\\Project\\Control\\Button');
jq_control_gen($baseUrl."/checkboxradio", 'Checkbox', 'QCubed\\Project\\Control\\Checkbox');
jq_control_gen($baseUrl."/checkboxradio", 'RadioButton', 'QCubed\\Project\\Control\\RadioButton');
jq_control_gen($baseUrl."/controlgroup");
jq_control_gen($baseUrl."/datepicker");
jq_control_gen($baseUrl."/datepicker", 'DatepickerBox', 'QCubed\\Project\\Control\\TextBox');
jq_control_gen($baseUrl."/dialog");
jq_control_gen($baseUrl."/progressbar");
jq_control_gen($baseUrl."/slider");
jq_control_gen($baseUrl."/tabs");
jq_control_gen($baseUrl."/menu");
jq_control_gen($baseUrl."/spinner", null, 'QCubed\\Project\\Control\\TextBox');
jq_control_gen($baseUrl."/selectmenu", 'SelectMenu', 'QCubed\\Project\\Control\\ListBox');
//jq_control_gen($baseUrl."/Tooltip"); A JQuery UI tool tip is not a control, but rather is straight javascript that changes how tooltips work on a whole page. Implementation would need to be very different.


