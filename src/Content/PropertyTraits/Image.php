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
        $isInstance = $this->image instanceof ImagePayload;

        if (! $isInstance) {
            $this->image = new ImagePayload();
        }

        // We have to do this to make PHPStan happy because it doesn't
        // understand that the condition above makes sure we always send
        // an instance of ImagePayload
        /** @var ImagePayload $image */
        $image = $this->image;

        return $image;
    }
}
