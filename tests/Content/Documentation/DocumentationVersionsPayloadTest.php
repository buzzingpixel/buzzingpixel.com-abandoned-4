<?php

declare(strict_types=1);

namespace Tests\Content\Documentation;

use App\Content\Documentation\DocumentationVersionsPayload;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DocumentationVersionsPayloadTest extends TestCase
{
    public function testWhenNoSoftwareInfo() : void
    {
        self::expectException(InvalidArgumentException::class);

        self::expectExceptionMessage('SoftwareInfo is required');

        new DocumentationVersionsPayload();
    }
}
