<?php

declare(strict_types=1);

namespace Tests\Users\Services;

use App\Payload\Payload;
use App\Stripe\Stripe;
use App\Users\Models\UserModel;
use App\Users\Services\SaveUser;
use App\Users\Services\UpdateStripeCustomer;
use Exception;
use PHPUnit\Framework\TestCase;
use Stripe\Customer;
use Throwable;

class Test extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testWhenNoStripeId() : void
    {
        $user = new UserModel();

        $user->emailAddress = 'foo@bar.baz';

        $stripeCustomer = new Customer('fooStripeId');

        $stripe = $this->createMock(Stripe::class);

        $stripe->expects(self::once())
            ->method('createCustomer')
            ->with(self::equalTo(['email' => 'foo@bar.baz']))
            ->willReturn($stripeCustomer);

        $saveUser = $this->createMock(SaveUser::class);

        $saveUser->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($user))
            ->willReturn(new Payload(Payload::STATUS_SUCCESSFUL));

        $service = new UpdateStripeCustomer(
            $stripe,
            $saveUser
        );

        $service($user);
    }

    /**
     * @throws Throwable
     */
    public function testWhenNoStripeUser() : void
    {
        $user = new UserModel();

        $user->stripeId = 'fooUserStripeId';

        $user->emailAddress = 'foo@bar.baz';

        $stripeCustomer = new Customer('fooStripeId');

        $stripe = $this->createMock(Stripe::class);

        $stripe->expects(self::at(0))
            ->method('retrieveCustomer')
            ->with(self::equalTo($user->stripeId))
            ->willThrowException(new Exception('Test Exception'));

        $stripe->expects(self::at(1))
            ->method('createCustomer')
            ->with(self::equalTo(['email' => 'foo@bar.baz']))
            ->willReturn($stripeCustomer);

        $saveUser = $this->createMock(SaveUser::class);

        $saveUser->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($user))
            ->willReturn(new Payload(Payload::STATUS_SUCCESSFUL));

        $service = new UpdateStripeCustomer(
            $stripe,
            $saveUser
        );

        $service($user);
    }

    /**
     * @throws Throwable
     */
    public function testWhenEmailsMatch() : void
    {
        $user = new UserModel();

        $user->stripeId = 'fooUserStripeId';

        $user->emailAddress = 'foo@bar.baz';

        $stripeCustomer = new Customer('fooStripeId');

        $stripeCustomer->email = 'foo@bar.baz';

        $stripe = $this->createMock(Stripe::class);

        $stripe->expects(self::at(0))
            ->method('retrieveCustomer')
            ->with(self::equalTo($user->stripeId))
            ->willReturn($stripeCustomer);

        $saveUser = $this->createMock(SaveUser::class);

        $saveUser->expects(self::never())
            ->method(self::anything());

        $service = new UpdateStripeCustomer(
            $stripe,
            $saveUser
        );

        $service($user);
    }

    /**
     * @throws Throwable
     */
    public function testWhenEmailsDontMatch() : void
    {
        $user = new UserModel();

        $user->stripeId = 'fooUserStripeId';

        $user->emailAddress = 'foo@bar.baz';

        $stripeCustomer = new Customer('fooStripeId');

        $stripeCustomer->email = 'bar@foo.baz';

        $stripe = $this->createMock(Stripe::class);

        $stripe->expects(self::at(0))
            ->method('retrieveCustomer')
            ->with(self::equalTo($user->stripeId))
            ->willReturn($stripeCustomer);

        $stripe->expects(self::at(1))
            ->method('updateCustomer')
            ->with(
                self::equalTo($stripeCustomer->id),
                self::equalTo([
                    'email' => $user->emailAddress,
                ])
            )
            ->willReturn($stripeCustomer);

        $saveUser = $this->createMock(SaveUser::class);

        $saveUser->expects(self::never())
            ->method(self::anything());

        $service = new UpdateStripeCustomer(
            $stripe,
            $saveUser
        );

        $service($user);
    }
}
