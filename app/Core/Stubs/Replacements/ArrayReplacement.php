<?php

namespace App\Core\Stubs\Replacements;

use App\Core\Contracts\Stubs\StubReplacement;
use App\Core\Helpers\IO\IO;

class ArrayReplacement extends StubReplacement
{

    private StubReplacement $stubReplacement;

    public function getReplacement(): StubReplacement
    {
        return $this->stubReplacement;
    }

    public function setReplacement(StubReplacement $stubReplacement): ArrayReplacement
    {
        $this->stubReplacement = $stubReplacement;
        return $this;
    }

    protected function askQuestion()
    {
        $completeArray = [];
        $continue = true;
        while($continue) {
            $singleValue = $this->stubReplacement->getValue();
            if(!IO::confirm('Would you like to add another value?')) {
                $continue = false;
            }
            $completeArray[] = $singleValue;
        }
        return $completeArray;
    }

    protected function validateType($value): bool
    {
        return is_array($value) && count($value) > 0;
    }

    public function parseCommandInput(string $variable): array
    {
        return array_map('trim', explode(',', $variable));
    }
}
