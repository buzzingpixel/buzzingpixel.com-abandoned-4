<?php

declare(strict_types=1);

namespace App\Content\PropertyTraits;

use App\Content\Modules\Payloads\ImageSourcePayload;
use InvalidArgumentException;

trait ImageProperties
{
    /** @var string */
    private $src = '';

    protected function setSrc(string $src) : void
    {
        $this->src = $src;
    }

    public function getSrc() : string
    {
        return $this->src;
    }

    /** @var string */
    private $srcset = '';

    protected function setSrcset(string $srcset) : void
    {
        $this->srcset = $srcset;
    }

    public function getSrcset() : string
    {
        return $this->srcset;
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
        foreach ($sources as $source) {
            if ($source instanceof ImageSourcePayload) {
                continue;
            }

            throw new InvalidArgumentException(
                'Source must be instance of ' . ImageSourcePayload::class
            );
        }

        $this->sources = $sources;
    }

    /**
     * @return ImageSourcePayload[]
     */
    public function getSources() : array
    {
        return $this->sources;
    }
}
