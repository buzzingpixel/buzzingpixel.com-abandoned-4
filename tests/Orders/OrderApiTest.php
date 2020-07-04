<?php

declare(strict_types=1);

namespace Tests\Orders;

use App\Orders\Models\OrderModel;
use App\Orders\OrderApi;
use App\Orders\Services\Fetch\FetchOrdersByIds\FetchOrdersByIds;
use App\Orders\Services\Fetch\FetchUserOrderByid\FetchUserOrderById;
use App\Orders\Services\Fetch\FetchUsersOrders\FetchUsersOrdersMaster;
use App\Orders\Services\SaveOrder\SaveOrderMaster;
use App\Payload\Payload;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class OrderApiTest extends TestCase
{
    public function testSaveOrder(): void
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

    public function testFetchOrdersByIds(): void
    {
        $orderModel = new OrderModel();

        $service = $this->createMock(FetchOrdersByIds::class);

        $service->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo(['foo-test-id']))
            ->willReturn([$orderModel]);

        $di = $this->createMock(ContainerInterface::class);

        $di->expects(self::once())
            ->method('get')
            ->with(self::equalTo(FetchOrdersByIds::class))
            ->willReturn($service);

        $api = new OrderApi($di);

        self::assertSame(
            [$orderModel],
            $api->fetchOrdersByIds(['foo-test-id']),
        );
    }

    public function testFetchCurrentUserOrders(): void
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

    public function testFetchCurrentUserOrderById(): void
    {
        $order = new OrderModel();

        $user = new UserModel();

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method('fetchLoggedInUser')
            ->willReturn($user);

        $service = $this->createMock(FetchUserOrderById::class);

        $service->expects(self::once())
            ->method('__invoke')
            ->with(
                self::equalTo($user),
                self::equalTo('foo-id'),
            )
            ->willReturn($order);

        $di = $this->createMock(ContainerInterface::class);

        $di->expects(self::at(0))
            ->method('get')
            ->with(self::equalTo(UserApi::class))
            ->willReturn($userApi);

        $di->expects(self::at(1))
            ->method('get')
            ->with(self::equalTo(FetchUserOrderById::class))
            ->willReturn($service);

        $api = new OrderApi($di);

        self::assertSame(
            $order,
            $api->fetchCurrentUserOrderById('foo-id')
        );
    }
}
