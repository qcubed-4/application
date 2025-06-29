<?php

namespace QCubed\Project\Codegen;

/**
 * CodeGen
 *
 * Overrides the Codegen\CodegenBase class.
 *
 * Feel free to override any of those methods here to customize your code generation.
 *
 */

/**
 * Class Codegen
 *
 * Overrides the default codegen class. Override and implement any functions here to customize the code generation process.
 */
class CodegenBase extends \QCubed\Codegen\CodegenBaseBase
{

    /**
     * Construct the CodeGen object.
     *
     * Gives you an opportunity to read your xml file and make codegen changes accordingly.
     */
    public function __construct($objSettingsXml)
    {
        $dirBase = dirname(__DIR__, 3);
        // Specify the paths to your template files here. These paths will be searched in the order declared, to
        // find a particular template file. Template files found lower down in the order will override the previous ones.
        static::$TemplatePaths = array(
            $dirBase . '/codegen/templates/',
            $dirBase . '/vendor/qcubed/orm/codegen/templates/'
        );
    }

    /**
     * CodeGen::pluralize()
     *
     * Example: Overriding the Pluralize method
     *
     * @param string $strName
     * @return string
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
