<?php

declare(strict_types=1);

namespace App\Orders\Transformers;

use App\Orders\Models\OrderModel;
use App\Persistence\Orders\OrderRecord;
use DateTimeInterface;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class TransformOrderModelToRecord
{
    public function __invoke(OrderModel $model): OrderRecord
    {
        $record = new OrderRecord();

        $record->id = $model->id;

        $record->old_order_number = $model->oldOrderNumber;

        if ($model->user !== null) {
            $record->user_id = $model->user->id;
        }

        $record->stripe_id = $model->stripeId;

        $record->stripe_amount = $model->stripeAmount;

        $record->stripe_balance_transaction = $model->stripeBalanceTransaction;

        $record->stripe_captured = $model->stripeCaptured ? '1' : '0';

        $record->stripe_created = $model->stripeCreated;

        $record->stripe_currency = $model->stripeCurrency;

        $record->stripe_paid = $model->stripePaid ? '1' : '0';

        $record->subtotal = $model->subtotal;

        $record->tax = $model->tax;

        $record->total = $model->total;

        $record->name = $model->name;

        $record->company = $model->company;

        $record->phone_number = $model->phoneNumber;

        $record->country = $model->country;

        $record->address = $model->address;

        $record->address_continued = $model->addressContinued;

        $record->city = $model->city;

        $record->state = $model->state;

        $record->postal_code = $model->postalCode;

        $record->date = $model->date->format(DateTimeInterface::ATOM);

        return $record;
    }
}
