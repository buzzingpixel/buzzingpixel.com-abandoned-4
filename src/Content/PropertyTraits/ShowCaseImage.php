<?php

declare(strict_types=1);

namespace App\Content\PropertyTraits;

use App\Content\Modules\Payloads\ImagePayload;

trait ShowCaseImage
{
    /** @var ImagePayload|null */
    private $showCaseImage;

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

        // We have to do this to make PHPStan happy because it doesn't
        // understand that the condition above makes sure we always send
        // an instance of ImagePayload
        /** @var ImagePayload $showCaseImage */
        $showCaseImage = $this->showCaseImage;

        return $showCaseImage;
    }
}
