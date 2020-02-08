<?php

declare(strict_types=1);

namespace App\Content\PropertyTraits;

use App\Content\Modules\Payloads\ImagePayload;
use function assert;

trait Image
{
    private ?ImagePayload $image = null;

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

        $image = $this->image;
        assert($image instanceof ImagePayload);

        return $image;
    }
}
