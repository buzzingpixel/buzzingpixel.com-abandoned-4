<?php

declare(strict_types=1);

namespace App\Cart\Services;

use App\Cart\Models\CartItemModel;
use App\Cart\Models\CartModel;
use App\Cart\Transformers\TransformCartItemRecordToModel;
use App\Cart\Transformers\TransformCartRecordToModel;
use App\Persistence\Cart\CartItemRecord;
use App\Persistence\Cart\CartRecord;
use App\Persistence\RecordQueryFactory;
use Throwable;

use function array_map;
use function assert;

class FetchCartById
{
    private RecordQueryFactory $recordQueryFactory;
    private TransformCartItemRecordToModel $transformCartItemRecordToModel;
    private TransformCartRecordToModel $transformCartRecordToModel;

    public function __construct(
        RecordQueryFactory $recordQueryFactory,
        TransformCartItemRecordToModel $transformCartItemRecordToModel,
        TransformCartRecordToModel $transformCartRecordToModel
    ) {
        $this->recordQueryFactory             = $recordQueryFactory;
        $this->transformCartItemRecordToModel = $transformCartItemRecordToModel;
        $this->transformCartRecordToModel     = $transformCartRecordToModel;
    }

    public function __invoke(string $id): ?CartModel
    {
        try {
            return $this->innerRun($id);
        } catch (Throwable $e) {
            return null;
        }
    }

    private function innerRun(string $id): ?CartModel
    {
        $query = ($this->recordQueryFactory)(new CartRecord())
            ->withWhere('id', $id)
            ->withWhere('user_id', null);

        $cartRecord = $query->one();

        assert(
            $cartRecord instanceof CartRecord || $cartRecord === null
        );

        if ($cartRecord === null) {
            return null;
        }

        /** @var CartItemRecord[] $itemRecords */
        $itemRecords = ($this->recordQueryFactory)(
            new CartItemRecord()
        )
            ->withWhere('cart_id', $cartRecord->id)
            ->withOrder('item_slug', 'asc')
            ->withOrder('id', 'asc')
            ->all();

        /** @var CartItemModel[] $itemModels */
        $itemModels = array_map(
            $this->transformCartItemRecordToModel,
            $itemRecords
        );

        return ($this->transformCartRecordToModel)(
            $cartRecord,
            $itemModels
        );
    }
}
