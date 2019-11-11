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
        if (! $this->showCaseImage) {
            $this->showCaseImage = new ImagePayload();
        }

        return $this->showCaseImage;
    }
}
