<?php

declare(strict_types=1);

namespace Tests\Persistence;

use PHPUnit\Framework\TestCase;

class RecordTest extends TestCase
{
    public function testStaticTableName() : void
    {
        $record = new HasStaticTableNameRecord();

        self::assertSame(
            'TestStaticTableName',
            $record->getTableName()
        );
    }
}
