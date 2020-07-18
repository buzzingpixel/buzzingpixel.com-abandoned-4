<?php

declare(strict_types=1);

namespace Tests\Stripe\Services;

use App\Stripe\Services\SaveNewCard;
use App\Users\Models\UserCardModel;
use App\Users\Models\UserModel;
use LogicException;
use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;
use Stripe\Card;
use Stripe\PaymentMethod;
use Stripe\Service\PaymentMethodService;
use Stripe\StripeClient;
use Throwable;

use function assert;

class SaveNewCardTest extends TestCase
{
    public function testWhenNoCardNumberAndNoCvC(): void
    {
        $card = new UserCardModel();

        $stripe = $this->createMock(StripeClient::class);

        $stripe->expects(self::never())
            ->method(self::anything());

        $service = new SaveNewCard($stripe);

        $exception = null;

        try {
            $service($card);
        } catch (Throwable $e) {
            $exception = $e;
        }

        assert($exception instanceof LogicException);

        self::assertSame(
            'Missing parameters',
            $exception->getMessage(),
        );
    }

    public function testWhenNoCardNumber(): void
    {
        $card = new UserCardModel();

        $card->newCvc = 'fooCvc';

        $stripe = $this->createMock(StripeClient::class);

        $stripe->expects(self::never())
            ->method(self::anything());

        $service = new SaveNewCard($stripe);

        $exception = null;

        try {
            $service($card);
        } catch (Throwable $e) {
            $exception = $e;
        }

        assert($exception instanceof LogicException);

        self::assertSame(
            'Missing parameters',
            $exception->getMessage(),
        );
    }

    public function testWhenNoCvc(): void
    {
        $card = new UserCardModel();

        $card->newCardNumber = 'fooCardNumber';

        $stripe = $this->createMock(StripeClient::class);

        $stripe->expects(self::never())
            ->method(self::anything());

        $service = new SaveNewCard($stripe);

        $exception = null;

        try {
            $service($card);
        } catch (Throwable $e) {
            $exception = $e;
        }

        assert($exception instanceof LogicException);

        self::assertSame(
            'Missing parameters',
            $exception->getMessage(),
        );
    }

    /**
     * @throws Throwable
     */
    public function test(): void
    {
        $expiration = DateTimeImmutable::createFromFormat(
            'Y-m-d',
            '2010-01-03',
        );

        $user = new UserModel();

        $user->stripeId = 'fooUserStripeId';

        $user->emailAddress = 'fooEmailAddress';

        $card = new UserCardModel();

        $card->user = $user;

        $card->stripeId = 'fooStripeId';

        $card->newCardNumber = 'fooNewCardNumber';

        $card->newCvc = 'fooCvc';

        $card->address = 'fooAddress';

        $card->address2 = 'fooAddress2';

        $card->city = 'fooCity';

        $card->state = 'fooState';

        $card->postalCode = 'fooPostalCode';

        $card->country = 'fooCountry';

        $card->nameOnCard = 'fooNameOnCard';

        $card->expiration = $expiration;

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
                    'cvc' => $card->newCvc,
                ],
                'billing_details' => [
                    'address' => [
                        'line1' => $card->address,
                        'line2' => $card->address2,
                        'city' => $card->city,
                        'state' => $card->state,
                        'postal_code' => $card->postalCode,
                        'country' => $card->country,
                    ],
                    'email' => $card->user->emailAddress,
                    'name' => $card->nameOnCard,
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

        $service = new SaveNewCard($stripe);

        $service($card);

        self::assertSame(
            'fooPaymentMethodId',
            $card->stripeId,
        );

        self::assertSame(
            'Foo Visa Card',
            $card->provider,
        );
    }
}
