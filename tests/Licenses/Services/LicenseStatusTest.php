<?php

declare(strict_types=1);

namespace Tests\Licenses\Services;

use App\Licenses\Models\LicenseModel;
use App\Licenses\Services\LicenseStatus;
use App\Utilities\SystemClock;
use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;

use function Safe\strtotime;

class LicenseStatusTest extends TestCase
{
    private LicenseStatus $service;

    private LicenseModel $license;

    protected function setUp(): void
    {
        $currentTime = new DateTimeImmutable();

        $clock = $this->createMock(SystemClock::class);

        $clock->method('getCurrentTime')
            ->willReturn($currentTime);

        $this->service = new LicenseStatus($clock);

        $this->license = new LicenseModel();
    }

    public function testWhenNotExpires(): void
    {
        self::assertNull(
            $this->service->daysUntilExpiration($this->license),
        );

        self::assertFalse(
            $this->service->isExpired($this->license),
        );

        self::assertSame(
            'active',
            $this->service->statusString($this->license),
        );
    }

    public function testWhenDisabled(): void
    {
        $expires = (new DateTimeImmutable())->setTimestamp(
            strtotime('+100 days'),
        );

        $this->license->expires = $expires;

        $this->license->isDisabled = true;

        self::assertSame(
            100,
            $this->service->daysUntilExpiration($this->license),
        );

        self::assertFalse(
            $this->service->isExpired($this->license),
        );

        self::assertSame(
            'disabled',
            $this->service->statusString($this->license),
        );
    }

    public function testWhenExpiresIn100Days(): void
    {
        $expires = (new DateTimeImmutable())->setTimestamp(
            strtotime('+100 days'),
        );

        $this->license->expires = $expires;

        self::assertSame(
            100,
            $this->service->daysUntilExpiration($this->license),
        );

        self::assertFalse(
            $this->service->isExpired($this->license),
        );

        self::assertSame(
            'active',
            $this->service->statusString($this->license),
        );
    }

    public function testWhenExpired(): void
    {
        $expires = (new DateTimeImmutable())->setTimestamp(
            strtotime('-1 day'),
        );

        $this->license->expires = $expires;

        self::assertSame(
            -1,
            $this->service->daysUntilExpiration($this->license),
        );

        self::assertTrue(
            $this->service->isExpired($this->license),
        );

        self::assertSame(
            'expired',
            $this->service->statusString($this->license),
        );
    }

    public function testWhenExpiresToday(): void
    {
        $expires = (new DateTimeImmutable())->setTimestamp(
            strtotime('+30 minutes'),
        );

        $this->license->expires = $expires;

        self::assertSame(
            0,
            $this->service->daysUntilExpiration($this->license),
        );

        self::assertFalse(
            $this->service->isExpired($this->license),
        );

        self::assertSame(
            'expires today!',
            $this->service->statusString($this->license),
        );
    }

    public function testWhenExpiresIn3Days(): void
    {
        $expires = (new DateTimeImmutable())->setTimestamp(
            strtotime('+3 days'),
        );

        $this->license->expires = $expires;

        self::assertSame(
            3,
            $this->service->daysUntilExpiration($this->license),
        );

        self::assertFalse(
            $this->service->isExpired($this->license),
        );

        self::assertSame(
            'expires in 3 days',
            $this->service->statusString($this->license),
        );
    }
}
