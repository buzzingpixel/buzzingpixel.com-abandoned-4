<?php

declare(strict_types=1);

namespace Tests\Orders\Transformers;

use App\Orders\Models\OrderItemModel;
use App\Orders\Transformers\TransformOrderRecordToModel;
use App\Persistence\Constants;
use App\Persistence\Orders\OrderRecord;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class TransformOrderRecordToModelTest extends TestCase
{
    public function test() : void
    {
        $user = new UserModel();

        $items = [
            new OrderItemModel(),
            new OrderItemModel(),
        ];

        $date = new DateTimeImmutable();

        $record                             = new OrderRecord();
        $record->id                         = 'foo-order-id';
        $record->old_order_number           = '123';
        $record->user_id                    = 'foo-user-id';
        $record->stripe_id                  = 'foo-stripe-id';
        $record->stripe_amount              = '543.2';
        $record->stripe_balance_transaction = 'foo-bal-transaction';
        $record->stripe_captured            = '1';
        $record->stripe_created             = 1;
        $record->stripe_currency            = 'USD';
        $record->stripe_paid                = true;
        $record->subtotal                   = '425.46';
        $record->tax                        = '1.34';
        $record->total                      = '5.43';
        $record->name                       = 'foo-name';
        $record->company                    = 'foo-company';
        $record->phone_number               = 'foo-phone-number';
        $record->country                    = 'foo-country';
        $record->address                    = 'foo-address';
        $record->address_continued          = 'foo-address-2';
        $record->city                       = 'foo-city';
        $record->state                      = 'foo-state';
        $record->postal_code                = 'foo-postal-code';
        $record->company                    = 'foo-company';
        $record->date                       = $date->format(
            Constants::POSTGRES_OUTPUT_FORMAT
        );

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method('fetchUserById')
            ->with(self::equalTo('foo-user-id'))
            ->willReturn($user);

        $transformer = new TransformOrderRecordToModel($userApi);

        $model = $transformer($record, $items);

        self::assertSame('foo-order-id', $model->id);

        self::assertSame(123, $model->oldOrderNumber);

        self::assertSame($user, $model->user);

        self::assertSame('foo-stripe-id', $model->stripeId);

        self::assertSame(543.2, $model->stripeAmount);

        self::assertSame(
            'foo-bal-transaction',
            $model->stripeBalanceTransaction
        );

        self::assertTrue($model->stripeCaptured);

        self::assertSame(1, $model->stripeCreated);

        self::assertSame('USD', $model->stripeCurrency);

        self::assertTrue($model->stripePaid);

        self::assertSame(425.46, $model->subtotal);

        self::assertSame(1.34, $model->tax);

        self::assertSame(5.43, $model->total);

        self::assertSame('foo-name', $model->name);

        self::assertSame('foo-company', $model->company);

        self::assertSame(
            'foo-phone-number',
            $model->phoneNumber
        );

        self::assertSame('foo-country', $model->country);

        self::assertSame('foo-address', $model->address);

        self::assertSame(
            'foo-address-2',
            $model->addressContinued
        );

        self::assertSame('foo-city', $model->city);

        self::assertSame('foo-state', $model->state);

        self::assertSame('foo-postal-code', $model->postalCode);

        self::assertSame('foo-company', $model->company);

        self::assertSame(
            $date->format(DateTimeInterface::ATOM),
            $model->date->format(DateTimeInterface::ATOM)
        );

        self::assertSame($items, $model->items);
    }
}
