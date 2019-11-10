<?php

declare(strict_types=1);

namespace Tests\App\Content\Modules\Payloads;

use App\Content\Modules\Payloads\CtaCardItemPayload;
use App\Content\Modules\Payloads\CtaCardsPayload;
use PHPUnit\Framework\TestCase;

class CtaCardsPayloadTest extends TestCase
{
    public function test() : void
    {
        $payload = new CtaCardsPayload();

        $primary1 = $payload->getPrimary();
        $primary2 = $payload->getPrimary();
        self::assertInstanceOf(CtaCardItemPayload::class, $primary1);
        self::assertSame($primary1, $primary2);

        $left1 = $payload->getLeft();
        $left2 = $payload->getLeft();
        self::assertInstanceOf(CtaCardItemPayload::class, $left1);
        self::assertSame($left1, $left2);

        $right1 = $payload->getRight();
        $right2 = $payload->getRight();
        self::assertInstanceOf(CtaCardItemPayload::class, $right1);
        self::assertSame($right1, $right2);
    }
}