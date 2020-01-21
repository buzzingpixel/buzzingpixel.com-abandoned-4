<?php

declare(strict_types=1);

namespace App\Cart\Transformers;

use App\Cart\Models\CartItemModel;
use App\Persistence\Cart\CartItemRecord;
use App\Software\Services\FetchSoftwareBySlug;

class TransformCartItemRecordToModel
{
    /** @var FetchSoftwareBySlug */
    private $fetchSoftwareBySlug;

    public function __construct(FetchSoftwareBySlug $fetchSoftwareBySlug)
    {
        $this->fetchSoftwareBySlug = $fetchSoftwareBySlug;
    }

    public function __invoke(CartItemRecord $record) : CartItemModel
    {
        return new CartItemModel([
            'id' => $record->id,
            'software' => ($this->fetchSoftwareBySlug)($record->item_slug),
            'quantity' => (int) $record->quantity,
        ]);
    }
}