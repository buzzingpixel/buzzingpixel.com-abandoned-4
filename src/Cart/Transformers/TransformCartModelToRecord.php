<?php

declare(strict_types=1);

namespace App\Cart\Transformers;

use App\Cart\Models\CartModel;
use App\Persistence\Cart\CartRecord;
use DateTimeInterface;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class TransformCartModelToRecord
{
    public function __invoke(CartModel $model) : CartRecord
    {
        $record = new CartRecord();

        $record->id = $model->id;

        $user = $model->user;

        if ($user !== null) {
            $record->user_id = $user->id;
        }

        $record->total_items = (string) $model->totalItems;

        $record->total_quantity = (string) $model->totalQuantity;

        $record->created_at = $model->createdAt->format(
            DateTimeInterface::ATOM
        );

        return $record;
    }
}
