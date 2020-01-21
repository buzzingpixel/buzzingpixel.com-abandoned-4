<?php

declare(strict_types=1);

namespace App\Cart\Transformers;

use App\Cart\Models\CartItemModel;
use App\Cart\Models\CartModel;
use App\Persistence\Cart\CartRecord;
use App\Persistence\Constants;
use App\Users\Services\FetchUserById;
use DateTimeImmutable;

class TransformCartRecordToModel
{
    /** @var FetchUserById */
    private $fetchUserById;

    public function __construct(FetchUserById $fetchUserById)
    {
        $this->fetchUserById = $fetchUserById;
    }

    /**
     * @param CartItemModel[] $items
     */
    public function __invoke(CartRecord $record, array $items) : CartModel
    {
        $user = null;

        if ($record->user_id !== null && $record->user_id !== '') {
            $user = ($this->fetchUserById)($record->user_id);
        }

        return new CartModel([
            'id' => $record->id,
            'user' => $user,
            'totalItems' => (int) $record->total_items,
            'totalQuantity' => (int) $record->total_quantity,
            'items' => $items,
            'createdAt' => DateTimeImmutable::createFromFormat(
                Constants::POSTGRES_OUTPUT_FORMAT,
                $record->created_at
            ),
        ]);
    }
}
