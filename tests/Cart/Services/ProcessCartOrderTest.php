<?php

declare(strict_types=1);

namespace Tests\Cart\Services;

use _HumbugBox89320708a2e3\Nette\Neon\Exception;
use App\Cart\Models\CartModel;
use App\Cart\Models\ProcessOrderModel;
use App\Cart\OrderProcessors\ChargeOrderToCard;
use App\Cart\OrderProcessors\ClearCartAtEnd;
use App\Cart\OrderProcessors\PopulateOrderAfterCharge;
use App\Cart\OrderProcessors\SaveOrderAfterPopulate;
use App\Cart\Services\ProcessCartOrder;
use App\Payload\Payload;
use App\Persistence\UuidFactoryWithOrderedTimeCodec;
use App\Users\Models\UserCardModel;
use App\Users\Models\UserModel;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use stdClass;
use Tests\TestConfig;
use Throwable;

class ProcessCartOrderTest extends TestCase
{
    public function testThrow(): void
    {
        $cart = new CartModel();

        $card = new UserCardModel();

        $di = $this->createMock(ContainerInterface::class);

        $di->method(self::anything())
            ->willThrowException(new Exception());

        $service = new ProcessCartOrder($di);

        $payload = $service($cart, $card);

        self::assertSame(
            Payload::STATUS_ERROR,
            $payload->getStatus(),
        );

        self::assertSame(
            [],
            $payload->getResult(),
        );
    }

    /**
     * @throws Throwable
     */
    public function test(): void
    {
        $user = new UserModel();

        $cart = new CartModel();

        $cart->user = $user;

        $card = new UserCardModel();

        $uuid = TestConfig::$di->get(
            UuidFactoryWithOrderedTimeCodec::class
        )->uuid1();

        $uuidFactory = $this->createMock(
            UuidFactoryWithOrderedTimeCodec::class
        );

        $uuidFactory->method('uuid1')
            ->willReturn($uuid);

        $holder = new stdClass();

        $holder->processOrderModel = null;

        $chargeOrderToCard = $this->createMock(
            ChargeOrderToCard::class
        );

        $chargeOrderToCard->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(static function (
                ProcessOrderModel $processOrderModel
            ) use ($holder): ProcessOrderModel {
                self::assertNull($holder->processOrderModel);

                $holder->processOrderModel = $processOrderModel;

                return $processOrderModel;
            });

        $populateOrderAfterCharge = $this->createMock(
            PopulateOrderAfterCharge::class
        );

        $populateOrderAfterCharge->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(static function (
                ProcessOrderModel $processOrderModel
            ) use ($holder): ProcessOrderModel {
                self::assertSame(
                    $holder->processOrderModel,
                    $processOrderModel,
                );

                return $processOrderModel;
            });

        $saveOrderAfterPopulate = $this->createMock(
            SaveOrderAfterPopulate::class
        );

        $saveOrderAfterPopulate->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(static function (
                ProcessOrderModel $processOrderModel
            ) use ($holder): ProcessOrderModel {
                self::assertSame(
                    $holder->processOrderModel,
                    $processOrderModel,
                );

                return $processOrderModel;
            });

        $clearCartAtEnd = $this->createMock(
            ClearCartAtEnd::class
        );

        $clearCartAtEnd->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(static function (
                ProcessOrderModel $processOrderModel
            ) use (
                $holder,
                $uuid,
                $cart,
                $card,
                $user
            ): ProcessOrderModel {
                self::assertSame(
                    $holder->processOrderModel,
                    $processOrderModel,
                );

                self::assertSame(
                    $uuid->toString(),
                    $processOrderModel->order()->id,
                );

                self::assertSame(
                    $cart,
                    $processOrderModel->cart(),
                );

                self::assertSame(
                    $card,
                    $processOrderModel->card(),
                );

                self::assertSame(
                    $user,
                    $processOrderModel->cart()->user,
                );

                return $processOrderModel;
            });

        $di = $this->createMock(ContainerInterface::class);

        $di->method('get')
            ->willReturnCallback(
                static function (string $class) use (
                    $uuidFactory,
                    $chargeOrderToCard,
                    $populateOrderAfterCharge,
                    $saveOrderAfterPopulate,
                    $clearCartAtEnd
                ) {
                    switch ($class) {
                        case UuidFactoryWithOrderedTimeCodec::class:
                            return $uuidFactory;

                        case ChargeOrderToCard::class:
                            return $chargeOrderToCard;

                        case PopulateOrderAfterCharge::class:
                            return $populateOrderAfterCharge;

                        case SaveOrderAfterPopulate::class:
                            return $saveOrderAfterPopulate;

                        case ClearCartAtEnd::class:
                            return $clearCartAtEnd;

                        default:
                            return null;
                    }
                }
            );

        $service = new ProcessCartOrder($di);

        $payload = $service($cart, $card);

        self::assertSame(
            Payload::STATUS_SUCCESSFUL,
            $payload->getStatus(),
        );

        self::assertSame(
            ['orderId' => $uuid->toString()],
            $payload->getResult(),
        );
    }
}
