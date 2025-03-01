<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed;

require_once(dirname(QCUBED_BASE_DIR) . '/ezyang/htmlpurifier/library/HTMLPurifier.auto.php');


/**
 * Singleton purifier service to encapsulate the default purification of data submitted by users in Web forms.
 */

class Purifier {
    public static $default_xss_setting; // ???

    protected $objConfig;
    protected $objPurifier;

    public function __construct()
    {
        $this->objConfig = self::config();
    }

    protected function config() {
        $objHTMLPurifierConfig = \HTMLPurifier_Config::createDefault();
        $objHTMLPurifierConfig->set('HTML.ForbiddenElements',
            'script,applet,embed,style,link,iframe,body,object');
        $objHTMLPurifierConfig->set('HTML.ForbiddenAttributes',
            '*@onfocus,*@onblur,*@onkeydown,*@onkeyup,*@onkeypress,*@onmousedown,*@onmouseup,*@onmouseover,*@onmouseout,*@onmousemove,*@onclick');

        if (defined('QCUBED_PURIFIER_CACHE_DIR') && is_dir(QCUBED_PURIFIER_CACHE_DIR)) {
            $objHTMLPurifierConfig->set('Cache.SerializerPath', QCUBED_PURIFIER_CACHE_DIR);
        } else {
            # Disable the cache entirely
            $objHTMLPurifierConfig->set('Cache.DefinitionImpl', null);
        }

        return $objHTMLPurifierConfig;
    }

    public function purify($strText, $objCustomConfig = null) {
        if ($objCustomConfig) {
            $objPurifier = new \HTMLPurifier($objCustomConfig);
        } else {
            if (!$this->objPurifier) {
                $this->objPurifier = new \HTMLPurifier($this->objConfig);
            }
            $objPurifier = $this->objPurifier;
        }

        // HTML Purifier does an html_encode, which is not what we usually want.
        return html_entity_decode($objPurifier->purify($strText));

    }
}