<?php

declare(strict_types=1);

namespace Tests\Stripe\EventListeners;

use App\Stripe\EventListeners\OnAfterDeleteUserCard;
use App\Users\Events\DeleteUserCardAfterDelete;
use App\Users\Models\UserCardModel;
use Exception;
use PHPUnit\Framework\TestCase;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentMethod;
use Stripe\Service\PaymentMethodService;
use Stripe\StripeClient;

class OnAfterDeleteUserCardTest extends TestCase
{
    public function testWhenStripeThrowsGenericException() : void
    {
        $card = new UserCardModel();

        $card->stripeId = 'foo-stripe-id';

        $afterDelete = new DeleteUserCardAfterDelete($card);

        $paymentMethods = $this->createMock(
            PaymentMethodService::class
        );

        $paymentMethods->expects(self::once())
            ->method('retrieve')
            ->with(self::equalTo('foo-stripe-id'))
            ->willThrowException(new Exception());

        $stripe = $this->createMock(StripeClient::class);

        $stripe->method('__get')
            ->with(self::equalTo('paymentMethods'))
            ->willReturn($paymentMethods);

        $service = new OnAfterDeleteUserCard($stripe);

        $service->onAfterDeleteUserCard($afterDelete);

        self::assertFalse($afterDelete->isValid);
    }

    public function testWhenNoStripeCard() : void
    {
        $card = new UserCardModel();

        $card->stripeId = 'foo-stripe-id';

        $afterDelete = new DeleteUserCardAfterDelete($card);

        $paymentMethods = $this->createMock(
            PaymentMethodService::class
        );

        $paymentMethods->expects(self::once())
            ->method('retrieve')
            ->with(self::equalTo('foo-stripe-id'))
            ->willThrowException($this->createMock(
                ApiErrorException::class,
            ));

        $stripe = $this->createMock(StripeClient::class);

        $stripe->method('__get')
            ->with(self::equalTo('paymentMethods'))
            ->willReturn($paymentMethods);

        $service = new OnAfterDeleteUserCard($stripe);

        $service->onAfterDeleteUserCard($afterDelete);

        self::assertTrue($afterDelete->isValid);
    }

    public function test() : void
    {
        $card = new UserCardModel();

        $card->stripeId = 'foo-stripe-id';

        $afterDelete = new DeleteUserCardAfterDelete($card);

        $paymentMethods = $this->createMock(
            PaymentMethodService::class
        );

        $paymentMethods->expects(self::at(0))
            ->method('retrieve')
            ->with(self::equalTo('foo-stripe-id'))
            ->willReturn(new PaymentMethod());

        $paymentMethods->expects(self::at(1))
            ->method('detach')
            ->with(self::equalTo('foo-stripe-id'))
            ->willReturn(new PaymentMethod());

        $stripe = $this->createMock(StripeClient::class);

        $stripe->method('__get')
            ->with(self::equalTo('paymentMethods'))
            ->willReturn($paymentMethods);

        $service = new OnAfterDeleteUserCard($stripe);

        $service->onAfterDeleteUserCard($afterDelete);

        self::assertTrue($afterDelete->isValid);
    }
}
