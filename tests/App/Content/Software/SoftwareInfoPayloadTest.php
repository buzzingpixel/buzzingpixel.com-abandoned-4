<?php

declare(strict_types=1);

namespace Tests\App\Content\Software;

use App\Content\Modules\Payloads\CtaPayload;
use App\Content\Software\SoftwareInfoPayload;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Throwable;

class SoftwareInfoPayloadTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testInvalidCtaPayloadInstanceInput() : void
    {
        self::expectException(InvalidArgumentException::class);

        self::expectExceptionMessage(
            'Action button must be instance of ' . CtaPayload::class
        );

        new SoftwareInfoPayload([
            'actionButtons' => ['test'],
        ]);
    }
}
