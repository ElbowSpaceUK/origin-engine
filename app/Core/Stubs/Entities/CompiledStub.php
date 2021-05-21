<?php

namespace App\Core\Stubs\Entities;

class CompiledStub
{

    protected StubFile $stubFile;

    protected string $content;

    /**
     * @return StubFile
     */
    public function getStubFile(): StubFile
    {
        return $this->stubFile;
    }

    /**
     * @param StubFile $stubFile
     * @return CompiledStub
     */
    public function setStubFile(StubFile $stubFile): CompiledStub
    {
        $this->stubFile = $stubFile;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return CompiledStub
     */
    public function setContent(string $content): CompiledStub
    {
        $this->content = $content;
        return $this;
    }

}
