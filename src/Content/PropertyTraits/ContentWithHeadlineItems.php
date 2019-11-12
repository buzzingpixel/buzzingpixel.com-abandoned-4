<?php

declare(strict_types=1);

namespace App\Content\PropertyTraits;

use App\Content\Modules\Payloads\ContentWithHeadingPayload;
use function array_walk;

trait ContentWithHeadlineItems
{
    /** @var ContentWithHeadingPayload[] */
    private $items = [];

    /**
     * @param ContentWithHeadingPayload[] $items
     */
    protected function setItems(array $items) : void
    {
        array_walk($items, [$this, 'setItem']);
    }

    private function setItem(ContentWithHeadingPayload $item) : void
    {
        $this->items[] = $item;
    }

    /**
     * @return ContentWithHeadingPayload[]
     */
    public function getItems() : array
    {
        return $this->items;
    }
}
