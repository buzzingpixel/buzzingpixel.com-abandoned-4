<?php

declare(strict_types=1);

namespace Tests\Content\Software;

use App\Content\Software\SoftwareInfoPayload;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class SoftwareInfoPayloadTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function test() : void
    {
        $payload = new SoftwareInfoPayload(['slug' => 'Test Slug']);

        self::assertSame('Test Slug', $payload->getSlug());
    }
}
