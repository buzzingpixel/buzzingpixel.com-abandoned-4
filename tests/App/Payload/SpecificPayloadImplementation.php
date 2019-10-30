<?php

declare(strict_types=1);

namespace Tests\App\Payload;

use App\Payload\SpecificPayload;

class SpecificPayloadImplementation extends SpecificPayload
{
    /** @var mixed */
    private $bar;

    /**
     * @param mixed $val
     */
    protected function setBar($val) : void
    {
        $this->bar = $val;
    }

    /**
     * @return mixed
     */
    public function getBar()
    {
        return $this->bar;
    }
}
