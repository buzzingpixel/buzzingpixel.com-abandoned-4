<?php

declare(strict_types=1);

namespace Tests\Analytics;

use App\Analytics\AnalyticsApi;
use App\Analytics\Models\AnalyticsModel;
use App\Analytics\Services\CreatePageView;
use App\Analytics\Services\GetTotalPageViewsSince;
use App\Analytics\Services\GetUniqueTotalVisitorsSince;
use App\Payload\Payload;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Safe\DateTimeImmutable;

class AnalyticsApiTest extends TestCase
{
    public function testCreatePageView(): void
    {
        $payload = new Payload(Payload::STATUS_CREATED);

        $model = new AnalyticsModel();

        $service = $this->createMock(CreatePageView::class);

        $service->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($model))
            ->willReturn($payload);

        $di = $this->createMock(ContainerInterface::class);

        $di->expects(self::once())
            ->method('get')
            ->with(self::equalTo(CreatePageView::class))
            ->willReturn($service);

        $api = new AnalyticsApi($di);

        self::assertSame(
            $payload,
            $api->createPageView($model)
        );
    }

    public function testGetTotalPageViewsSince(): void
    {
        $date = new DateTimeImmutable();

        $service = $this->createMock(
            GetTotalPageViewsSince::class
        );

        $service->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($date))
            ->willReturn(987);

        $di = $this->createMock(ContainerInterface::class);

        $di->expects(self::once())
            ->method('get')
            ->with(self::equalTo(
                GetTotalPageViewsSince::class
            ))
            ->willReturn($service);

        $api = new AnalyticsApi($di);

        self::assertSame(
            987,
            $api->getTotalPageViewsSince($date)
        );
    }

    public function testGetUniqueTotalVisitorsSince(): void
    {
        $date = new DateTimeImmutable();

        $service = $this->createMock(
            GetUniqueTotalVisitorsSince::class
        );

        $service->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($date))
            ->willReturn(243);

        $di = $this->createMock(ContainerInterface::class);

        $di->expects(self::once())
            ->method('get')
            ->with(self::equalTo(
                GetUniqueTotalVisitorsSince::class
            ))
            ->willReturn($service);

        $api = new AnalyticsApi($di);

        self::assertSame(
            243,
            $api->getUniqueTotalVisitorsSince($date)
        );
    }
}
