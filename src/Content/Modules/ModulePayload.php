<?php

declare(strict_types=1);

namespace App\Content\Modules;

use App\Payload\SpecificPayload;

class ModulePayload extends SpecificPayload
{
    /** @var SpecificPayload[] */
    private array $items = [];

    /**
     * @param SpecificPayload[] $items
     */
    protected function setItems(array $items): void
    {
        $this->items = $items;
    }

    /**
     * @return SpecificPayload[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
