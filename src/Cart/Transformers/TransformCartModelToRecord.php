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

        $record->id = $model->getId();

        $user = $model->getUser();

        if ($user !== null) {
            $record->user_id = $user->id;
        }

        $record->total_items = (string) $model->getTotalItems();

        $record->total_quantity = (string) $model->getTotalQuantity();

        $record->created_at = $model->getCreatedAt()->format(
            DateTimeInterface::ATOM
        );

        return $record;
    }
}
