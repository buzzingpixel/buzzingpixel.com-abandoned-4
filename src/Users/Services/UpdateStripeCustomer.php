<?php

declare(strict_types=1);

namespace App\Users\Services;

use App\Stripe\Stripe;
use App\Users\Models\UserModel;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Throwable;

class UpdateStripeCustomer
{
    private Stripe $stripe;
    private SaveUser $saveUser;

    public function __construct(
        Stripe $stripe,
        SaveUser $saveUser
    ) {
        $this->stripe   = $stripe;
        $this->saveUser = $saveUser;
    }

    /**
     * @throws ApiErrorException
     */
    public function __invoke(UserModel $user) : void
    {
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

        $this->stripe->updateCustomer($stripeUser->id, [
            'email' => $user->emailAddress,
        ]);
    }

    /**
     * @throws ApiErrorException
     */
    private function createStripeUser(UserModel $user) : void
    {
        $stripeUser = $this->stripe->createCustomer([
            'email' => $user->emailAddress,
        ]);

        $user->stripeId = $stripeUser->id;

        ($this->saveUser)($user);
    }

    private function retrieveStripeUser(UserModel $user) : ?Customer
    {
        try {
            return $this->stripe->retrieveCustomer($user->stripeId);
        } catch (Throwable $e) {
            return null;
        }
    }
}
