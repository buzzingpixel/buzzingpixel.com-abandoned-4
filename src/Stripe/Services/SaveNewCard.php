<?php

declare(strict_types=1);

namespace App\Stripe\Services;

use App\Users\Models\UserCardModel;
use LogicException;
use Stripe\StripeClient;
use Throwable;

use function ucwords;

class SaveNewCard
{
    private StripeClient $stripe;

    public function __construct(StripeClient $stripe)
    {
        $this->stripe = $stripe;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(UserCardModel $card): void
    {
        if ($card->newCardNumber === '' || $card->newCvc === '') {
            throw new LogicException('Missing parameters');
        }

        $responseCard = $this->stripe->paymentMethods->create([
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
        ]);

        $this->stripe->paymentMethods->attach(
            $responseCard->id,
            ['customer' => $card->user->stripeId]
        );

        $card->stripeId = $responseCard->id;

        $responseCardCard = $responseCard->card;

        /** @phpstan-ignore-next-line */
        $card->provider = ucwords((string) $responseCardCard->brand);
    }
}
