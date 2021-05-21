<?php

namespace App\Core\Stubs\Replacements;

use App\Core\Contracts\Stubs\StubReplacement;
use App\Core\Helpers\IO\IO;

class BooleanReplacement extends StubReplacement
{

    protected function askQuestion(): bool
    {
        return IO::confirm(
            $this->getQuestionText(),
            $this->getDefault(true)
        );
    }

    public function validateType($value): bool
    {
        return is_bool($value);
    }

    public function parseCommandInput(string $variable): bool
    {
        $true = ['1', 'true', 'on', 'yes'];
        $false = ['0', 'false', 'off', 'no'];
        if(in_array($variable, $true)) {
            return true;
        }
        if(in_array($variable, $false)) {
            return false;
        }
        return (bool) $variable;
    }
}
