<?php

declare(strict_types=1);

namespace App\Stripe\EventListeners;

use App\Stripe\Services\SaveExistingCard;
use App\Stripe\Services\SaveNewCard;
use App\Stripe\Services\UpdateStripeCustomer;
use App\Users\Events\SaveUserCardBeforeSave;
use Throwable;

class OnBeforeSaveUserCard
{
    private UpdateStripeCustomer $updateStripeCustomer;
    private SaveNewCard $saveNewCard;
    private SaveExistingCard $saveExistingCard;

    public function __construct(
        UpdateStripeCustomer $updateStripeCustomer,
        SaveNewCard $saveNewCard,
        SaveExistingCard $saveExistingCard
    ) {
        $this->updateStripeCustomer = $updateStripeCustomer;
        $this->saveNewCard          = $saveNewCard;
        $this->saveExistingCard     = $saveExistingCard;
    }

    public function onBeforeSaveUserCard(
        SaveUserCardBeforeSave $beforeSave
    ): void {
        try {
            ($this->updateStripeCustomer)(
                $beforeSave->userCardModel->user
            );

            $card = $beforeSave->userCardModel;

            if ($card->stripeId === '') {
                ($this->saveNewCard)($card);

                return;
            }

            ($this->saveExistingCard)($card);
        } catch (Throwable $e) {
            $beforeSave->isValid = false;
        }
    }
}
