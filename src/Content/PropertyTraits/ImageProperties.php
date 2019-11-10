<?php

declare(strict_types=1);

namespace App\Content\PropertyTraits;

use App\Content\Modules\Payloads\ImageSourcePayload;
use function array_walk;

trait ImageProperties
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
    private $alt = '';

    protected function setAlt(string $alt) : void
    {
        $this->alt = $alt;
    }

    public function getAlt() : string
    {
        return $this->alt;
    }

    /** @var ImageSourcePayload[] */
    private $sources = [];

    /**
     * @param ImageSourcePayload[] $sources
     */
    protected function setSources(array $sources) : void
    {
        array_walk($sources, [$this, 'addSource']);
    }

    private function addSource(ImageSourcePayload $source) : void
    {
        $this->sources[] = $source;
    }

    /**
     * @return ImageSourcePayload[]
     */
    public function getSources() : array
    {
        return $this->sources;
    }
}
