<?php

declare(strict_types=1);

namespace App\Content\PropertyTraits;

use App\Content\Modules\Payloads\ShowCaseImagePayload;

trait ShowCaseImage
{
    /** @var ShowCaseImagePayload|null */
    private $showCaseImage;

    protected function setShowCaseImage(ShowCaseImagePayload $showCaseImage) : void
    {
        $this->showCaseImage = $showCaseImage;
    }

    public function getShowCaseImage() : ShowCaseImagePayload
    {
        if (! $this->showCaseImage) {
            $this->showCaseImage = new ShowCaseImagePayload();
        }

        return $this->showCaseImage;
    }
}
