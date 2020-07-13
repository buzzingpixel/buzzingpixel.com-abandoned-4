<?php

declare(strict_types=1);

namespace Tests\Subscriptions;

use App\Payload\Payload;
use App\Subscriptions\Models\SubscriptionModel;
use App\Subscriptions\Services\SaveSubscription;
use App\Subscriptions\SubscriptionApi;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class SubscriptionApiTest extends TestCase
{
    public function testSaveSubscription(): void
    {
        $payload = new Payload(Payload::STATUS_ERROR);

        $model = new SubscriptionModel();

        $service = $this->createMock(SaveSubscription::class);

        $service->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($model))
            ->willReturn($payload);

        $di = $this->createMock(ContainerInterface::class);

        $di->expects(self::once())->method('get')
            ->with(self::equalTo(SaveSubscription::class))
            ->willReturn($service);

        $api = new SubscriptionApi($di);

        self::assertSame(
            $payload,
            $api->saveSubscription($model),
        );
    }
}
