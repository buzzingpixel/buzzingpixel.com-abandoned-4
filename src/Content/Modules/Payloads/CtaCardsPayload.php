<?php

declare(strict_types=1);

namespace App\Content\Modules\Payloads;

use App\Payload\SpecificPayload;

class CtaCardsPayload extends SpecificPayload
{
    /** @var CtaCardItemPayload|null */
    private $primary;

    protected function setPrimary(CtaCardItemPayload $primary) : void
    {
        $this->primary = $primary;
    }

    public function getPrimary() : CtaCardItemPayload
    {
        if (! $this->primary) {
            $this->primary = new CtaCardItemPayload();
        }

        return $this->primary;
    }

    /** @var CtaCardItemPayload|null */
    private $left;

    protected function setLeft(CtaCardItemPayload $left) : void
    {
        $this->left = $left;
    }

    public function getLeft() : CtaCardItemPayload
    {
        if (! $this->left) {
            $this->left = new CtaCardItemPayload();
        }

        return $this->left;
    }

    /** @var CtaCardItemPayload|null */
    private $right;

    protected function setRight(CtaCardItemPayload $right) : void
    {
        $this->right = $right;
    }

    public function getRight() : CtaCardItemPayload
    {
        if (! $this->right) {
            $this->right = new CtaCardItemPayload();
        }

        return $this->right;
    }
}
