<?php

declare(strict_types=1);

namespace App\Content\PropertyTraits;

use App\Content\Modules\Payloads\ImageSourcePayload;
use function array_walk;

trait ImageProperties
{
    private string $oneX = '';

    protected function setOneX(string $oneX) : void
    {
        $this->oneX = $oneX;
    }

    public function getOneX() : string
    {
        return $this->oneX;
    }

    private string $twoX = '';

    protected function setTwoX(string $twoX) : void
    {
        $this->twoX = $twoX;
    }

    public function getTwoX() : string
    {
        return $this->twoX;
    }

    private string $alt = '';

    protected function setAlt(string $alt) : void
    {
        $this->alt = $alt;
    }

    public function getAlt() : string
    {
        return $this->alt;
    }

    /** @var ImageSourcePayload[] */
    private array $sources = [];

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
