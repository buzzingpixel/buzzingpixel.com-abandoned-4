<?php

declare(strict_types=1);

namespace App\Content\Documentation;

use App\Payload\SpecificPayload;

class ListPayload extends SpecificPayload
{
    /** @var mixed[] */
    private $listItems = [];

    /**
     * @param mixed[] $listItems
     */
    protected function setListItems(array $listItems) : void
    {
        $this->listItems = $listItems;
    }

    /**
     * @return mixed[]
     */
    public function getListItems() : array
    {
        return $this->listItems;
    }
}
