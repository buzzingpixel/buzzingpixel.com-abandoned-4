<?php

declare(strict_types=1);

namespace App\Cart\Transformers;

use App\Cart\Models\CartItemModel;
use App\Persistence\Cart\CartItemRecord;
use App\Software\Services\FetchSoftwareBySlug;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class TransformCartItemRecordToModel
{
    private FetchSoftwareBySlug $fetchSoftwareBySlug;

    public function __construct(FetchSoftwareBySlug $fetchSoftwareBySlug)
    {
        $this->fetchSoftwareBySlug = $fetchSoftwareBySlug;
    }

    public function __invoke(CartItemRecord $record) : CartItemModel
    {
        $cartModel = new CartItemModel();

        $cartModel->id = $record->id;

        $cartModel->software = ($this->fetchSoftwareBySlug)(
            $record->item_slug
        );

        $cartModel->quantity = (int) $record->quantity;

        return $cartModel;
    }
}
