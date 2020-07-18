<?php

declare(strict_types=1);

namespace Tests\Stripe\Services;

use App\Stripe\Services\UpdateStripeCustomer;
use App\Users\Models\UserModel;
use Exception;
use PHPUnit\Framework\TestCase;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\Service\CustomerService;
use Stripe\StripeClient;
use Throwable;

class UpdateStripeCustomerTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testWhenApiErrorException() : void
    {
        $user = new UserModel();

        $exception = $this->createMock(
            ApiErrorException::class
        );

        $stripe = $this->createMock(StripeClient::class);

        $stripe->expects(self::once())
            ->method('__get')
            ->with(self::equalTo('customers'))
            ->willThrowException($exception);

        $service = new UpdateStripeCustomer($stripe);

        $service($user);
    }

    /**
     * @throws Throwable
     */
    public function testWhenNoStripeId() : void
    {
        $user = new UserModel();

        $user->emailAddress = 'foo@bar.baz';

        $stripeCustomer = new Customer('fooStripeId');

        $customerService = $this->createMock(CustomerService::class);

        $customerService->expects(self::once())
            ->method('create')
            ->with(self::equalTo([
                'email' => $user->emailAddress,
            ]))
            ->willReturn($stripeCustomer);

        $stripe = $this->createMock(StripeClient::class);

        $stripe->expects(self::once())
            ->method('__get')
            ->with(self::equalTo('customers'))
            ->willReturn($customerService);

        $service = new UpdateStripeCustomer($stripe);

        $service($user);

        self::assertSame(
            $stripeCustomer->id,
            $user->stripeId
        );
    }

    /**
     * @throws Throwable
     */
    public function testWhenNoStripeUser() : void
    {
        $user = new UserModel();

        $user->stripeId = 'foo-retrieve-stripe-id';

        $user->emailAddress = 'foo@bar.baz';

        $stripeCustomer = new Customer('fooStripeId');

        $customerService = $this->createMock(CustomerService::class);

        $customerService->expects(self::at(0))
            ->method('retrieve')
            ->with(self::equalTo($user->stripeId))
            ->willThrowException(new Exception());

        $customerService->expects(self::at(1))
            ->method('create')
            ->with(self::equalTo([
                'email' => $user->emailAddress,
            ]))
            ->willReturn($stripeCustomer);

        $stripe = $this->createMock(StripeClient::class);

        $stripe->method('__get')
            ->with(self::equalTo('customers'))
            ->willReturn($customerService);

        $service = new UpdateStripeCustomer($stripe);

        $service($user);

        self::assertSame(
            $stripeCustomer->id,
            $user->stripeId
        );
    }

    /**
     * @throws Throwable
     */
    public function testWhenEmailsMatch() : void
    {
        $user = new UserModel();

        $user->stripeId = 'fooStripeId';

        $user->emailAddress = 'foo@bar.baz';

        $stripeCustomer = new Customer('fooStripeId');

        $stripeCustomer->email = 'foo@bar.baz';

        $customerService = $this->createMock(CustomerService::class);

        $customerService->expects(self::at(0))
            ->method('retrieve')
            ->with(self::equalTo($user->stripeId))
            ->willReturn($stripeCustomer);

        $stripe = $this->createMock(StripeClient::class);

        $stripe->method('__get')
            ->with(self::equalTo('customers'))
            ->willReturn($customerService);

        $service = new UpdateStripeCustomer($stripe);

        $service($user);

        self::assertSame(
            $stripeCustomer->id,
            $user->stripeId
        );
    }

    /**
     * @throws Throwable
     */
    public function testWhenEmailsDontMatch() : void
    {
        $user = new UserModel();

        $user->stripeId = 'fooStripeId';

        $user->emailAddress = 'foo@bar.baz';

        $stripeCustomer = new Customer('fooStripeId');

        $stripeCustomer->email = 'bar@foo.baz';

        $customerService = $this->createMock(CustomerService::class);

        $customerService->expects(self::at(0))
            ->method('retrieve')
            ->with(self::equalTo($user->stripeId))
            ->willReturn($stripeCustomer);

        $customerService->expects(self::at(1))
            ->method('update')
            ->with(
                self::equalTo($stripeCustomer->id),
                self::equalTo([
                    'email' => $user->emailAddress,
                ])
            )
            ->willReturn($stripeCustomer);

        $stripe = $this->createMock(StripeClient::class);

        $stripe->method('__get')
            ->with(self::equalTo('customers'))
            ->willReturn($customerService);

        $service = new UpdateStripeCustomer($stripe);

        $service($user);

        self::assertSame(
            $stripeCustomer->id,
            $user->stripeId
        );
    }
}
