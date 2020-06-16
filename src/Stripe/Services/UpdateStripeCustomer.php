<?php

declare(strict_types=1);

namespace App\Stripe\Services;

use App\Users\Models\UserModel;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use Throwable;

class UpdateStripeCustomer
{
    private StripeClient $stripe;

    public function __construct(StripeClient $stripe)
    {
        $this->stripe = $stripe;
    }

    public function __invoke(UserModel $user) : void
    {
        try {
            if ($user->stripeId === '') {
                $this->createStripeUser($user);

                return;
            }

            $stripeUser = $this->retrieveStripeUser($user);

            if ($stripeUser === null) {
                $this->createStripeUser($user);

                return;
            }

            if ($stripeUser->email === $user->emailAddress) {
                return;
            }

            $this->stripe->customers->update($stripeUser->id, [
                'email' => $user->emailAddress,
            ]);
        } catch (ApiErrorException $e) {
        }
    }

    /**
     * @throws ApiErrorException
     */
    private function createStripeUser(UserModel $user) : void
    {
        $stripeCustomer = $this->stripe->customers->create([
            'email' => $user->emailAddress,
        ]);

        $user->stripeId = $stripeCustomer->id;
    }

    private function retrieveStripeUser(UserModel $user) : ?Customer
    {
        try {
            return $this->stripe->customers->retrieve($user->stripeId);
        } catch (Throwable $e) {
            return null;
        }
    }
}
