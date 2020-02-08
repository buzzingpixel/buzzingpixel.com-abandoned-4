<?php

declare(strict_types=1);

namespace App\Content\PropertyTraits;

use App\Content\Modules\Payloads\ImagePayload;
use function assert;

trait ShowCaseImage
{
    private ?ImagePayload $showCaseImage = null;

    protected function setShowCaseImage(ImagePayload $showCaseImage) : void
    {
        $this->showCaseImage = $showCaseImage;
    }

    public function getShowCaseImage() : ImagePayload
    {
        $isInstance = $this->showCaseImage instanceof ImagePayload;

        if (! $isInstance) {
            $this->showCaseImage = new ImagePayload();
        }

        $showCaseImage = $this->showCaseImage;
        assert($showCaseImage instanceof ImagePayload);

        return $showCaseImage;
    }
}
