<?php

namespace OriginEngine\Stubs\Registrar;

use OriginEngine\Contracts\Stubs\StubReplacement;
use OriginEngine\Stubs\Entities\Stub;
use OriginEngine\Stubs\Entities\StubFile;
use OriginEngine\Stubs\StubStore;

class StubFileRegistrar
{

    /**
     * @var StubFile
     */
    private StubFile $stubFile;

    public function __construct(StubFile $stubFile)
    {
        $this->stubFile = $stubFile;
    }

    public static function registerStubFile(string $stubPath, $fileName, ?string $relativeLocation = null, ?\Closure $showIf = null): StubFileRegistrar
    {
        $stubFile = new StubFile();
        $stubFile->setStubPath($stubPath);
        $stubFile->setFileName($fileName);
        $stubFile->setLocation($relativeLocation);
        $stubFile->setShowIf($showIf);

        return new static($stubFile);
    }

    public function addReplacement(StubReplacement $replacement): StubFileRegistrar
    {
        $replacements = $this->stubFile->getReplacements();
        $replacements[] = $replacement;
        $this->stubFile->setReplacements($replacements);
        return $this;
    }

    public function getStubFile(): StubFile
    {
        return $this->stubFile;
    }

}
