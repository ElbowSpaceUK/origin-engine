<?php

namespace OriginEngine\Plugins\Stubs;

use OriginEngine\Helpers\IO\IO;
use OriginEngine\Plugins\Stubs\Entities\CollectedStubData;
use OriginEngine\Plugins\Stubs\Entities\Stub;

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
                $stubFiles[] = clone $stubFile;
            } else {
                continue;
            }

            foreach($stubFile->getReplacements() as $replacement) {
                if(!array_key_exists($replacement->getVariableName(), $data)) {
                    $data = $replacement->appendData($data, $useDefault);
                }
            }
        }

        // Set the stub file filenames if using a callback
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
