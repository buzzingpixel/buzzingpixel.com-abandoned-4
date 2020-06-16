<?php

declare(strict_types=1);

namespace Tests\Stripe\EventListeners;

use App\Stripe\EventListeners\OnBeforeSaveUserCard;
use App\Stripe\Services\UpdateStripeCustomer;
use App\Users\Events\SaveUserCardBeforeSave;
use App\Users\Models\UserCardModel;
use App\Users\Models\UserModel;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Stripe\Card;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentMethod;
use Stripe\Service\PaymentMethodService;
use Stripe\StripeClient;
use function assert;

class OnBeforeSaveUserCardTest extends TestCase
{
    public function testWhenNoCardNumberAndNoId() : void
    {
        $user = new UserModel();

        $card = new UserCardModel();

        $card->user = $user;

        $beforeSave = new SaveUserCardBeforeSave($card);

        $stripe = $this->createMock(StripeClient::class);

        $stripe->expects(self::never())
            ->method(self::anything());

        $updateStripeCustomer = $this->createMock(
            UpdateStripeCustomer::class
        );

        $onBeforeSaveUserCard = new OnBeforeSaveUserCard(
            $stripe,
            $updateStripeCustomer,
        );

        $onBeforeSaveUserCard->onBeforeSaveUserCard($beforeSave);

        self::assertFalse($beforeSave->isValid);
    }

    public function testWhenNoCardNumber() : void
    {
        $user = new UserModel();

        $card = new UserCardModel();

        $card->user = $user;

        $card->stripeId = 'fooStripeId';

        $beforeSave = new SaveUserCardBeforeSave($card);

        $stripe = $this->createMock(StripeClient::class);

        $stripe->expects(self::never())
            ->method(self::anything());

        $updateStripeCustomer = $this->createMock(
            UpdateStripeCustomer::class
        );

        $onBeforeSaveUserCard = new OnBeforeSaveUserCard(
            $stripe,
            $updateStripeCustomer,
        );

        $onBeforeSaveUserCard->onBeforeSaveUserCard($beforeSave);

        self::assertTrue($beforeSave->isValid);
    }

    public function testWhenNewCardNumber() : void
    {
        $expiration = DateTimeImmutable::createFromFormat(
            'Y-m-d',
            '2010-01-03',
        );

        assert($expiration instanceof DateTimeImmutable);

        $user = new UserModel();

        $user->stripeId = 'fooUserStripeId';

        $card = new UserCardModel();

        $card->user = $user;

        $card->stripeId = 'fooStripeId';

        $card->newCardNumber = 'fooNewCardNumber';

        $card->expiration = $expiration;

        $beforeSave = new SaveUserCardBeforeSave($card);

        $stripeCard = new Card();

        $stripeCard->brand = 'foo visa card';

        $paymentMethod = new PaymentMethod('fooPaymentMethodId');

        $paymentMethod->card = $stripeCard;

        $paymentMethods = $this->createMock(
            PaymentMethodService::class,
        );

        $paymentMethods->expects(self::at(0))
            ->method('create')
            ->with(self::equalTo([
                'type' => 'card',
                'card' => [
                    'number' => $card->newCardNumber,
                    'exp_month' => $card->expiration->format('n'),
                    'exp_year' => $card->expiration->format('Y'),
                ],
            ]))
            ->willReturn($paymentMethod);

        $paymentMethods->expects(self::at(1))
            ->method('attach')
            ->with(
                'fooPaymentMethodId',
                ['customer' => 'fooUserStripeId'],
            )
            ->willReturn($paymentMethod);

        $stripe = $this->createMock(StripeClient::class);

        $stripe->method('__get')
            ->with(self::equalTo('paymentMethods'))
            ->willReturn($paymentMethods);

        $updateStripeCustomer = $this->createMock(
            UpdateStripeCustomer::class
        );

        $onBeforeSaveUserCard = new OnBeforeSaveUserCard(
            $stripe,
            $updateStripeCustomer,
        );

        $onBeforeSaveUserCard->onBeforeSaveUserCard($beforeSave);

        self::assertTrue($beforeSave->isValid);

        self::assertSame(
            'fooPaymentMethodId',
            $card->stripeId,
        );

        self::assertSame(
            'Foo Visa Card',
            $card->provider,
        );
    }

    public function testWhenApiErrorExceptionThrown() : void
    {
        $expiration = DateTimeImmutable::createFromFormat(
            'Y-m-d',
            '2010-01-03',
        );

        assert($expiration instanceof DateTimeImmutable);

        $user = new UserModel();

        $user->stripeId = 'fooUserStripeId';

        $card = new UserCardModel();

        $card->user = $user;

        $card->stripeId = 'fooStripeId';

        $card->newCardNumber = 'fooNewCardNumber';

        $card->expiration = $expiration;

        $beforeSave = new SaveUserCardBeforeSave($card);

        $apiErrorException = $this->createMock(
            ApiErrorException::class
        );

        $paymentMethods = $this->createMock(
            PaymentMethodService::class,
        );

        $paymentMethods->expects(self::at(0))
            ->method('create')
            ->with(self::equalTo([
                'type' => 'card',
                'card' => [
                    'number' => $card->newCardNumber,
                    'exp_month' => $card->expiration->format('n'),
                    'exp_year' => $card->expiration->format('Y'),
                ],
            ]))
            ->willThrowException($apiErrorException);

        $stripe = $this->createMock(StripeClient::class);

        $stripe->method('__get')
            ->with(self::equalTo('paymentMethods'))
            ->willReturn($paymentMethods);

        $updateStripeCustomer = $this->createMock(
            UpdateStripeCustomer::class
        );

        $onBeforeSaveUserCard = new OnBeforeSaveUserCard(
            $stripe,
            $updateStripeCustomer,
        );

        $onBeforeSaveUserCard->onBeforeSaveUserCard($beforeSave);

        self::assertFalse($beforeSave->isValid);
    }
}
