<?php

declare(strict_types=1);

namespace Tests\Orders;

use App\Orders\Models\OrderModel;
use App\Orders\OrderApi;
use App\Orders\Services\Fetch\FetchUsersOrdersMaster;
use App\Orders\Services\SaveOrder\SaveOrderMaster;
use App\Payload\Payload;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class OrderApiTest extends TestCase
{
    public function testSaveOrder() : void
    {
        $payload = new Payload(Payload::STATUS_CREATED);

        $orderModel = new OrderModel();

        $saveOrder = $this->createMock(SaveOrderMaster::class);

        $saveOrder->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($orderModel))
            ->willReturn($payload);

        $di = $this->createMock(ContainerInterface::class);

        $di->expects(self::once())
            ->method('get')
            ->with(self::equalTo(SaveOrderMaster::class))
            ->willReturn($saveOrder);

        $api = new OrderApi($di);

        $returnPayload = $api->saveOrder($orderModel);

        self::assertSame($payload, $returnPayload);
    }

    public function testFetchCurrentUserOrders() : void
    {
        $orderModels = [
            new OrderModel(),
            new OrderModel(),
        ];

        $user = new UserModel();

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method('fetchLoggedInUser')
            ->willReturn($user);

        $fetchUsersOrdersMaster = $this->createMock(
            FetchUsersOrdersMaster::class
        );

        $fetchUsersOrdersMaster->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($user))
            ->willReturn($orderModels);

        $di = $this->createMock(ContainerInterface::class);

        $di->expects(self::at(0))
            ->method('get')
            ->with(self::equalTo(UserApi::class))
            ->willReturn($userApi);

        $di->expects(self::at(1))
            ->method('get')
            ->with(self::equalTo(
                FetchUsersOrdersMaster::class
            ))
            ->willReturn($fetchUsersOrdersMaster);

        $api = new OrderApi($di);

        self::assertSame(
            $orderModels,
            $api->fetchCurrentUserOrders()
        );
    }
}
