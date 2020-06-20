<?php

declare(strict_types=1);

namespace App\Orders\Transformers;

use App\Orders\Models\OrderItemModel;
use App\Orders\Models\OrderModel;
use App\Persistence\Constants;
use App\Persistence\Orders\OrderRecord;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use Safe\DateTimeImmutable;
use function assert;
use function in_array;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class TransformOrderRecordToModel
{
    private UserApi $userApi;

    public function __construct(
        UserApi $userApi
    ) {
        $this->userApi = $userApi;
    }

    /**
     * @param OrderItemModel[] $items
     */
    public function __invoke(
        OrderRecord $record,
        array $items = [],
        ?UserModel $user = null
    ) : OrderModel {
        $model = new OrderModel();

        $model->id = $record->id;

        $model->oldOrderNumber = (int) $record->old_order_number;

        if ($user === null || $user->id !== $record->user_id) {
            $user = $this->userApi->fetchUserById($record->user_id);
        }

        assert($user instanceof UserModel);

        $model->user = $user;

        $model->stripeId = $record->stripe_id;

        $model->stripeAmount = (float) $record->stripe_amount;

        $model->stripeBalanceTransaction = $record->stripe_balance_transaction;

        $model->stripeCaptured = in_array(
            $record->stripe_captured,
            ['1', 1, true],
            true
        );

        $stripeCreated = in_array(
            $record->stripe_created,
            ['1', 1, true],
            true
        );

        $model->stripeCreated = $stripeCreated ? 1 : 0;

        $model->stripeCurrency = $record->stripe_currency;

        $model->stripePaid = in_array(
            $record->stripe_paid,
            ['1', 1, true],
            true
        );

        $model->subtotal = (float) $record->subtotal;

        $model->tax = (float) $record->tax;

        $model->total = (float) $record->total;

        $model->name = $record->name;

        $model->company = $record->company;

        $model->phoneNumber = $record->phone_number;

        $model->country = $record->country;

        $model->address = $record->address;

        $model->addressContinued = $record->address_continued;

        $model->city = $record->city;

        $model->state = $record->state;

        $model->postalCode = $record->postal_code;

        $model->company = $record->company;

        $date = DateTimeImmutable::createFromFormat(
            Constants::POSTGRES_OUTPUT_FORMAT,
            $record->date,
        );

        $model->date = $date;

        $model->items = $items;

        return $model;
    }
}
