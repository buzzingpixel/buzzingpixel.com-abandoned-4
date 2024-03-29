<?php

declare(strict_types=1);

namespace App\Cart\Services;

use App\Cart\Models\CartItemModel;
use App\Cart\Models\CartModel;
use App\Cart\Transformers\TransformCartItemModelToRecord;
use App\Cart\Transformers\TransformCartModelToRecord;
use App\Payload\Payload;
use App\Persistence\Cart\CartItemRecord;
use App\Persistence\SaveExistingRecord;
use App\Persistence\SaveNewRecord;
use App\Persistence\UuidFactoryWithOrderedTimeCodec;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use PDO;
use Safe\DateTimeImmutable;
use Throwable;

use function array_walk;
use function count;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

/**
 * TODO: Excluding this from code coverage for now. It's a huge class/method
 * and I don't have time to refactor it at this time
 */
class SaveCart
{
    private PDO $pdo;
    private UuidFactoryWithOrderedTimeCodec $uuidFactory;
    private TransformCartModelToRecord $transformCartModelToRecord;
    private TransformCartItemModelToRecord $transformCartItemModelToRecord;
    private SaveNewRecord $saveNewRecord;
    private SaveExistingRecord $saveExistingRecord;

    public function __construct(
        PDO $pdo,
        UuidFactoryWithOrderedTimeCodec $uuidFactory,
        TransformCartModelToRecord $transformCartModelToRecord,
        TransformCartItemModelToRecord $transformCartItemModelToRecord,
        SaveNewRecord $saveNewRecord,
        SaveExistingRecord $saveExistingRecord
    ) {
        $this->pdo                            = $pdo;
        $this->uuidFactory                    = $uuidFactory;
        $this->transformCartModelToRecord     = $transformCartModelToRecord;
        $this->transformCartItemModelToRecord = $transformCartItemModelToRecord;
        $this->saveNewRecord                  = $saveNewRecord;
        $this->saveExistingRecord             = $saveExistingRecord;
    }

    public function __invoke(CartModel $cart): Payload
    {
        try {
            $this->pdo->beginTransaction();

            return $this->innerRun($cart);
        } catch (Throwable $e) {
            $this->pdo->rollBack();

            return new Payload(Payload::STATUS_ERROR);
        }
    }

    private int $totalQuantity = 0;

    /**
     * @throws Throwable
     */
    private function innerRun(CartModel $cart): Payload
    {
        $this->totalQuantity = 0;

        $now = new DateTimeImmutable(
            'now',
            new DateTimeZone('UTC')
        );

        $nowString = $now->format(DateTimeInterface::ATOM);

        $isNew = false;

        if ($cart->id === '') {
            $isNew = true;

            $cart->id = $this->uuidFactory->uuid1()->toString();
        }

        $items = [];

        foreach ($cart->items as $item) {
            if ($item->quantity < 1) {
                $this->deleteItem($item);

                continue;
            }

            $items[] = $item;
        }

        $cart->items = $items;

        array_walk($items, [$this, 'saveItem']);

        $cart->totalItems = count($items);

        $cart->totalQuantity = $this->totalQuantity;

        $record = ($this->transformCartModelToRecord)($cart);

        $record->last_touched_at = $nowString;

        if ($isNew) {
            $record->created_at = $nowString;

            $payload = ($this->saveNewRecord)($record);

            if ($payload->getStatus() === Payload::STATUS_CREATED) {
                $this->pdo->commit();

                return $payload;
            }

            throw new Exception('Unknown error saving version');
        }

        $payload = ($this->saveExistingRecord)($record);

        if ($payload->getStatus() === Payload::STATUS_UPDATED) {
            $this->pdo->commit();

            return $payload;
        }

        throw new Exception('Unknown error saving version');
    }

    protected function deleteItem(CartItemModel $item): void
    {
        $statement = $this->pdo->prepare(
            'DELETE FROM ' . (new CartItemRecord())->getTableName() .
            ' WHERE id = :id'
        );

        $statement->execute([':id' => $item->id]);
    }

    /**
     * @throws Throwable
     */
    protected function saveItem(CartItemModel $item): void
    {
        $this->totalQuantity += $item->quantity;

        if ($item->id === '') {
            $item->id = $this->uuidFactory->uuid1()->toString();

            $record = ($this->transformCartItemModelToRecord)($item);

            $payload = ($this->saveNewRecord)($record);

            if ($payload->getStatus() === Payload::STATUS_CREATED) {
                return;
            }

            throw new Exception('Unknown error saving version');
        }

        $record = ($this->transformCartItemModelToRecord)($item);

        $payload = ($this->saveExistingRecord)($record);

        if ($payload->getStatus() === Payload::STATUS_UPDATED) {
            return;
        }

        throw new Exception('Unknown error saving version');
    }
}
