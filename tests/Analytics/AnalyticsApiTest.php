<?php

declare(strict_types=1);

namespace Tests\Analytics;

use App\Analytics\AnalyticsApi;
use App\Analytics\Models\AnalyticsModel;
use App\Analytics\Services\CreatePageView;
use App\Payload\Payload;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

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
}
