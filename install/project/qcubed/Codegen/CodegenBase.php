<?php
/**
 * CodeGen
 *
 * Overrides the Codegen\CodegenBaseBase class.
 *
 * Feel free to override any of those methods here to customize your code generation.
 *
 */

namespace QCubed\Project\Codegen;

use SimpleXmlElement;

/**
 * Class Codegen
 *
 * Overrides the default codegen class. Override and implement any functions here to customize the code generation process.
 * @package Project
 */
class CodegenBase extends \QCubed\Codegen\CodegenBase
{
    /**
     * Constructor method for initializing the class with the provided settings.
     *
     * @param mixed $objSettingsXml The settings parameter, typically an XML object or related data structure.
     * @return void
     */
    public function __construct(SimpleXmlElement $objSettingsXml)
    {
        parent::__construct($objSettingsXml);

        $this->objSettingsXml = $objSettingsXml;
        static::$TemplatePaths = $this->getInstalledTemplatePaths();
    }

    /**
     * Retrieves a list of installed template paths by merging parent-defined paths with custom paths.
     *
     * @return array An array of file paths where template files are located. Custom paths are appended to the end.
     */
    public function getInstalledTemplatePaths(): array
    {
        $paths = parent::getInstalledTemplatePaths();

        // Add the paths to your custom template files here. These paths will be searched in the order declared to
        // find a particular template file. Template files found lower down in the order will override the previous ones.
        $paths[] = QCUBED_PROJECT_DIR . '/codegen/templates/';
        return $paths;
    }

    /**
     * Converts a singular noun to its plural form, applying specific rules for exceptional cases.
     *
     * @param string $strName The singular noun to be pluralized.
     * @return string The pluralized form of the given noun.
     */
    protected function pluralize(string $strName): string
    {
        // Special Rules go Here
        return match (true) {
            $strName == 'person' => 'people',
            $strName == 'Person' => 'People',
            $strName == 'PERSON' => 'PEOPLE',
            strtolower($strName) == 'fish' => $strName . 'ies',
            default => parent::pluralize($strName),
        };
    }
}
