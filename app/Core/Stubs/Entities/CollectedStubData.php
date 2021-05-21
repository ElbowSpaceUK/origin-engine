<?php

namespace App\Core\Stubs\Entities;

class CollectedStubData
{

    private array $data;

    private array $stubFiles;

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return CollectedStubData
     */
    public function setData(array $data): CollectedStubData
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return array
     */
    public function getStubFiles(): array
    {
        return $this->stubFiles;
    }

    /**
     * @param array $stubFiles
     * @return CollectedStubData
     */
    public function setStubFiles(array $stubFiles): CollectedStubData
    {
        $this->stubFiles = $stubFiles;
        return $this;
    }



}
