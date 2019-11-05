<?php

declare(strict_types=1);

namespace App\Content\PropertyTraits;

use App\Content\Modules\Payloads\ImagePayload;

trait Image
{
    /** @var ImagePayload|null */
    private $image;

    protected function setImage(ImagePayload $image) : void
    {
        $this->image = $image;
    }

    public function getImage() : ImagePayload
    {
        if (! $this->image) {
            $this->image = new ImagePayload();
        }

        return $this->image;
    }
}
