<?php

declare(strict_types=1);

namespace Tests\Stripe\EventListeners;

use App\Stripe\EventListeners\OnBeforeSaveUser;
use App\Stripe\Services\UpdateStripeCustomer;
use App\Users\Events\SaveUserBeforeSave;
use App\Users\Models\UserModel;
use PHPUnit\Framework\TestCase;

class OnBeforeSaveUserTest extends TestCase
{
    public function test() : void
    {
        $userModel = new UserModel();

        $updateStripeCustomer = $this->createMock(
            UpdateStripeCustomer::class,
        );

        $updateStripeCustomer->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($userModel));

        $onBeforeSaveUser = new OnBeforeSaveUser(
            $updateStripeCustomer
        );

        $beforeSave = new SaveUserBeforeSave($userModel);

        $onBeforeSaveUser->onBeforeSaveUser($beforeSave);
    }
}
