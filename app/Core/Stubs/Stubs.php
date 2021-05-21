<?php

namespace App\Core\Stubs;

use App\Core\Contracts\Stubs\StubReplacement;
use App\Core\Stubs\Registrar\StubFileRegistrar;
use App\Core\Stubs\Registrar\StubRegistrar;
use App\Core\Stubs\Replacements\ArrayReplacement;
use App\Core\Stubs\Replacements\BooleanReplacement;
use App\Core\Stubs\Replacements\SectionReplacement;
use App\Core\Stubs\Replacements\StringReplacement;
use App\Core\Stubs\Replacements\TableColumnReplacement;

class Stubs
{

    public function newStub(string $name, string $description, string $defaultLocation = null): StubRegistrar
    {
        return StubRegistrar::registerStub($name, $description, $defaultLocation);
    }

    /**
     * @param string $stubPath
     * @param string|\Closure $fileName
     * @param string|null $relativeLocation
     * @param \Closure|null $showIf
     * @return StubFileRegistrar
     */
    public function newStubFile(string $stubPath, $fileName, ?string $relativeLocation = null, ?\Closure $showIf = null): StubFileRegistrar
    {
        return StubFileRegistrar::registerStubFile(
            $stubPath, $fileName, $relativeLocation, $showIf
        );
    }

    public function newSectionReplacement(string $variableName, string $questionText, $default = null, ?\Closure $validator = null, array $replacements = []): SectionReplacement
    {
        $replacement = SectionReplacement::new($variableName, $questionText, $default, $validator);
        $replacement->setReplacements($replacements);
        return $replacement;
    }

    public function newArrayReplacement(string $variableName, string $questionText, $default = null, ?\Closure $validator = null, StubReplacement $replacement): ArrayReplacement
    {
        $arrayReplacement = ArrayReplacement::new($variableName, $questionText, $default, $validator);
        $arrayReplacement->setReplacement($replacement);
        return $arrayReplacement;
    }

    public function newTableColumnReplacement(string $variableName, string $questionText, $default = null, ?\Closure $validator = null): StubReplacement
    {
        return TableColumnReplacement::new($variableName, $questionText, $default, $validator);
    }

    public function newStringReplacement(string $variableName, string $questionText, $default = null, ?\Closure $validator = null): StubReplacement
    {
        return StringReplacement::new($variableName, $questionText, $default, $validator);
    }

    public function newBooleanReplacement(string $variableName, string $questionText, $default = null, ?\Closure $validator = null): StubReplacement
    {
        return BooleanReplacement::new($variableName, $questionText, $default, $validator);
    }

}
