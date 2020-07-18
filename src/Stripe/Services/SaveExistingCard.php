<?php

declare(strict_types=1);

namespace App\Stripe\Services;

use App\Users\Models\UserCardModel;
use Stripe\StripeClient;
use Throwable;

class SaveExistingCard
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
        $this->stripe->paymentMethods->update(
            $card->stripeId,
            [
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
            ]
        );
    }
}
