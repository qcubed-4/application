<?php

use QCubed\Project\Control\DataGrid;
use QCubed\Project\Control\FormBase;
use QCubed\QString;

require_once('../qcubed.inc.php');

class PluginManagerForm extends FormBase
{
    protected $dtgPlugins;
    protected $btnNewPlugin;
    protected $dlgUpload;
    protected $lblMessage;

    protected function formCreate()
    {
        $this->dtgPlugins_Create();
    }

    private function dtgPlugins_Create()
    {
        $this->dtgPlugins = new DataGrid($this, 'dtgPlugins');
        $this->dtgPlugins->setDataBinder('dtgPlugins_Bind');

        $this->dtgPlugins->CssClass = 'datagrid';

        $this->dtgPlugins->createIndexedColumn(t('Name'), 'Name');
        $this->dtgPlugins->createIndexedColumn(t('Description'), 'Description');
        $col = $this->dtgPlugins->createCallableColumn(t('Examples'), [$this, 'renderExampleLink']);
        $col->HtmlEntities = false;

        if (!file_exists(dirname(QCUBED_BASE_DIR) . "/composer/installed.json")) {
            $this->dtgPlugins->Warning = "Could not find the composer 'installed.json' file.";
        }
    }

    public function dtgPlugins_Bind()
    {
        if (!file_exists(dirname(QCUBED_BASE_DIR) . "/composer/installed.json")) {
            return;
        }

        $string = file_get_contents(dirname(QCUBED_BASE_DIR) . "/composer/installed.json");
        $installed = json_decode($string, true);
        $offset = $installed['packages'];
        $qcubed = array_filter($offset,
            function($item) {
                return (!empty($item['type']) && $item['type'] == "qcubed-library");
            });

        $itemArray = [];
        foreach ($qcubed as $item) {
            $strComposerFilePath = dirname(QCUBED_BASE_DIR) . '/' . $item['name'] . '/' . 'composer.json';
            if (file_exists($strComposerFilePath)) {
                $composerDetails = json_decode(file_get_contents($strComposerFilePath), true);

                $arrayItem['Name'] = $item['name'];
                $arrayItem['Description'] = '';
                if (!empty($composerDetails['description'])) {
                    $arrayItem['Description'] = $composerDetails['description'];
                }
                $arrayItem['Examples'] = null;
                if (!empty($composerDetails['extra']['examples'])) { // embed example page name into composer file for convenience
                    foreach ($composerDetails['extra']['examples'] as $strExample) {
                        $strExamplePath = dirname(QCUBED_BASE_DIR) . '/' . $item['name'] . '/examples/' . $strExample;
                        if (file_exists($strExamplePath)) {
                            $arrayItem['Examples'][] = $strExample;
                        }
                    }
                }
                $itemArray[] = $arrayItem;
            }
        }
        $this->dtgPlugins->DataSource = $itemArray;
    }



    public function renderExampleLink($item)
    {
        if ($item['Examples']) {
            $strRet = '';
            foreach ($item['Examples'] as $strItem) {
                $strRet .= '<a href="' . dirname(QCUBED_BASE_URL) . '/' . $item['Name'] . '/examples/' . $strItem .
                    '">' . QString::htmlEntities($strItem) . '</a><br />';
            }
            return $strRet;
        }
        return null;
    }
}

PluginManagerForm::run('PluginManagerForm');