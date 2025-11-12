<?php
    /**
     *
     * Part of the QCubed PHP framework.
     *
     * @license MIT
     *
     */

    namespace QCubed;

    use HTMLPurifier_Config;
    use HTMLPurifier;

    require_once(dirname(QCUBED_BASE_DIR) . '/ezyang/htmlpurifier/library/HTMLPurifier.auto.php');


    /**
     * Singleton purifier service to encapsulate the default purification of data submitted by users in Web forms.
     */

    class Purifier {
        public static $default_xss_setting; // ???

        protected object $objConfig;
        protected ?object $objPurifier = null;

        public function __construct()
        {
            $this->objConfig = self::config();
        }

        /**
         * @return \HTMLPurifier_Config
         */
        protected function config(): HTMLPurifier_Config
        {
            $objHTMLPurifierConfig = HTMLPurifier_Config::createDefault();
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

        /**
         * @param string $strText
         * @param null|object $objCustomConfig
         *
         * @return string
         */
        public function purify(string $strText, ?object $objCustomConfig = null): string
        {
            if ($objCustomConfig) {
                $objPurifier = new HTMLPurifier($objCustomConfig);
            } else {
                if (!$this->objPurifier) {
                    $this->objPurifier = new HTMLPurifier($this->objConfig);
                }
                $objPurifier = $this->objPurifier;
            }

            // HTML Purifier does an HTML encode, which is not what we usually want.
            return html_entity_decode($objPurifier->purify($strText));

        }
    }