<?php

namespace OriginEngine\Stubs\Replacements;

use OriginEngine\Contracts\Stubs\StubReplacement;
use OriginEngine\Helpers\IO\IO;

class StringReplacement extends StubReplacement
{


    public function askQuestion(): string
    {
        return IO::ask(
            $this->getQuestionText(),
            $this->getDefault('')
        );
    }

    public function validateType($value): bool
    {
        return is_string($value) && strlen($value) > 0;
    }

}
