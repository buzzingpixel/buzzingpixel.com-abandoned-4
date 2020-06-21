<?php

declare(strict_types=1);

namespace Tests\Stripe\Services;

use App\Stripe\Services\SaveExistingCard;
use App\Users\Models\UserCardModel;
use App\Users\Models\UserModel;
use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;
use Stripe\PaymentMethod;
use Stripe\Service\PaymentMethodService;
use Stripe\StripeClient;
use Throwable;

class SaveExistingCardTest extends TestCase
{
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

        $paymentMethod = new PaymentMethod('fooPaymentMethodId');

        $paymentMethods = $this->createMock(
            PaymentMethodService::class,
        );

        $paymentMethods->expects(self::once())
            ->method('update')
            ->with(
                self::equalTo($card->stripeId),
                self::equalTo([
                    'card' => [
                        'exp_month' => $card->expiration->format('n'),
                        'exp_year' => $card->expiration->format('Y'),
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
                ])
            )
            ->willReturn($paymentMethod);

        $stripe = $this->createMock(StripeClient::class);

        $stripe->method('__get')
            ->with(self::equalTo('paymentMethods'))
            ->willReturn($paymentMethods);

        $service = new SaveExistingCard($stripe);

        $service($card);
    }
}
