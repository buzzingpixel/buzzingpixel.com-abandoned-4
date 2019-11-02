<?php

declare(strict_types=1);

namespace App\Content\Modules\Payloads;

use App\Payload\SpecificPayload;

class ImageSourcePayload extends SpecificPayload
{
    /** @var string */
    private $oneX = '';

    protected function setOneX(string $oneX) : void
    {
        $this->oneX = $oneX;
    }

    public function getOneX() : string
    {
        return $this->oneX;
    }

    /** @var string */
    private $twoX = '';

    protected function setTwoX(string $twoX) : void
    {
        $this->twoX = $twoX;
    }

    public function getTwoX() : string
    {
        return $this->twoX;
    }

    /** @var string */
    private $mediaQuery = '';

    protected function setMediaQuery(string $mediaQuery) : void
    {
        $this->mediaQuery = $mediaQuery;
    }

    public function getMediaQuery() : string
    {
        return $this->mediaQuery;
    }
}
