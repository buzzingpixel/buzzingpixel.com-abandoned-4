<?php

declare(strict_types=1);

namespace App\Cart\Services;

use App\Cart\Models\CartItemModel;
use App\Cart\Models\CartModel;
use App\Persistence\Cart\CartItemRecord;
use App\Persistence\Cart\CartRecord;
use PDO;
use function array_fill;
use function array_map;
use function count;
use function implode;

class DeleteCart
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function __invoke(CartModel $model) : void
    {
        $statement = $this->pdo->prepare(
            'DELETE FROM ' . (new CartRecord())->getTableName() .
            ' WHERE id = :id'
        );

        $statement->execute([':id' => $model->getId()]);

        $ids = array_map(
            static function (CartItemModel $model) : string {
                return $model->getId();
            },
            $model->getItems()
        );

        if (count($ids) < 1) {
            return;
        }

        $in = implode(
            ',',
            array_fill(0, count($ids), '?')
        );

        $itemTableName = (new CartItemRecord())->getTableName();

        $itemStatement = $this->pdo->prepare(
            'DELETE FROM ' . $itemTableName .
            ' WHERE id IN (' . $in . ')'
        );

        $itemStatement->execute($ids);
    }
}
