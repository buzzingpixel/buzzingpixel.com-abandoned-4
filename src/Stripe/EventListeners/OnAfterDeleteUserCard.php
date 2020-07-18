<?php

declare(strict_types=1);

namespace App\Stripe\EventListeners;

use App\Users\Events\DeleteUserCardAfterDelete;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use Throwable;

class OnAfterDeleteUserCard
{
    private StripeClient $stripeClient;

    public function __construct(
        StripeClient $stripeClient
    ) {
        $this->stripeClient = $stripeClient;
    }

    public function onAfterDeleteUserCard(
        DeleteUserCardAfterDelete $afterDelete
    ): void {
        try {
            $this->innerRun($afterDelete);
        } catch (Throwable $e) {
            $afterDelete->isValid = false;
        }
    }

    /**
     * @throws Throwable
     */
    private function innerRun(DeleteUserCardAfterDelete $afterDelete): void
    {
        try {
            // If we can't get the card, it doesn't exist on Stripe, and since
            // we want to delete it anyway, that's fine, I guess
            $this->stripeClient->paymentMethods->retrieve(
                $afterDelete->userCardModel->stripeId
            );
        } catch (ApiErrorException $e) {
            return;
        }

        $this->stripeClient->paymentMethods->detach(
            $afterDelete->userCardModel->stripeId
        );
    }
}
