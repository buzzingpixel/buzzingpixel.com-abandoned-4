<?php

declare(strict_types=1);

namespace App\Stripe\EventListeners;

use App\Stripe\Services\UpdateStripeCustomer;
use App\Users\Events\SaveUserCardBeforeSave;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use function ucwords;

class OnBeforeSaveUserCard
{
    private StripeClient $stripe;
    private UpdateStripeCustomer $updateStripeCustomer;

    public function __construct(
        StripeClient $stripe,
        UpdateStripeCustomer $updateStripeCustomer
    ) {
        $this->stripe               = $stripe;
        $this->updateStripeCustomer = $updateStripeCustomer;
    }

    public function onBeforeSaveUserCard(
        SaveUserCardBeforeSave $beforeSave
    ) : void {
        try {
            ($this->updateStripeCustomer)(
                $beforeSave->userCardModel->user
            );

            $card = $beforeSave->userCardModel;

            if ($card->newCardNumber === '' && $card->stripeId === '') {
                $beforeSave->isValid = false;

                return;
            }

            if ($card->newCardNumber === '') {
                return;
            }

            $responseCard = $this->stripe->paymentMethods->create([
                'type' => 'card',
                'card' => [
                    'number' => $card->newCardNumber,
                    'exp_month' => $card->expiration->format('n'),
                    'exp_year' => $card->expiration->format('Y'),
                ],
            ]);

            $this->stripe->paymentMethods->attach(
                $responseCard->id,
                ['customer' => $card->user->stripeId]
            );

            $card->stripeId = $responseCard->id;

            $responseCardCard = $responseCard->card;

            $card->provider = ucwords((string) $responseCardCard->brand);
        } catch (ApiErrorException $e) {
            $beforeSave->isValid = false;
        }
    }
}
