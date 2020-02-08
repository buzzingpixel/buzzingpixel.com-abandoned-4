<?php

declare(strict_types=1);

namespace Tests\Persistence;

use App\Persistence\Record;

class HasStaticTableNameRecord extends Record
{
    protected static string $tableName = 'TestStaticTableName';
}
