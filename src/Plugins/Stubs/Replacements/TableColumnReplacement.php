<?php

namespace OriginEngine\Plugins\Stubs\Replacements;

use OriginEngine\Contracts\Stubs\StubReplacement;
use OriginEngine\Helpers\IO\IO;

class TableColumnReplacement extends StubReplacement
{

    protected function askQuestion()
    {
        $tableColumn = new TableColumn();
        $tableColumn->setName(IO::ask('What is the column name?'));
        $tableColumn->setType(IO::choice('What is the column type?', [
            'string',
            'bool'
        ]));
        $tableColumn->setNullable(IO::confirm('Is the column nullable?'));
        return $tableColumn;
    }

    protected function validateType($value): bool
    {
        return $value instanceof TableColumn;
    }
}
