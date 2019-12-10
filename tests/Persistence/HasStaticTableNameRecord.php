<?php

declare(strict_types=1);

namespace Tests\Persistence;

use App\Persistence\Record;

class HasStaticTableNameRecord extends Record
{
    /** @var string */
    protected static $tableName = 'TestStaticTableName';
}
