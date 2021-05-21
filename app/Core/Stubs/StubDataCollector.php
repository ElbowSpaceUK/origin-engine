<?php

namespace App\Core\Stubs;

use App\Core\Helpers\IO\IO;
use App\Core\Stubs\Entities\CollectedStubData;
use App\Core\Stubs\Entities\Stub;

/**
 * Analyses the stub files to gather all dependencies.
 */
class StubDataCollector
{

    public function collect(Stub $stub, array $data = [], bool $useDefault = false): CollectedStubData
    {
        $stubFiles = [];
        foreach($stub->getStubFiles() as $stubFile) {
            if($useDefault === true || $stubFile->showIf($data)) {
                $stubFiles[] = $stubFile;
            } else {
                continue;
            }
            foreach($stubFile->getReplacements() as $replacement) {
                if(!array_key_exists($replacement->getVariableName(), $data)) {
                    $data = $replacement->appendData($data, $useDefault);
                }
            }
        }

        $stubFiles = array_map(function($stubFile) use ($data) {
            if(is_callable($stubFile->getFileName())) {
                $stubFile->setFileName($stubFile->getFileName()($data));
            }
            return $stubFile;
        }, $stubFiles);


        return (new CollectedStubData())
            ->setStubFiles($stubFiles)
            ->setData($data);
    }

}
