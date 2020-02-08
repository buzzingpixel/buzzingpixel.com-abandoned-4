<?php

declare(strict_types=1);

namespace App\Content\Modules\Payloads;

use App\Payload\SpecificPayload;
use function assert;

class CtaCardsPayload extends SpecificPayload
{
    private ?CtaCardItemPayload $primary = null;

    protected function setPrimary(CtaCardItemPayload $primary) : void
    {
        $this->primary = $primary;
    }

    public function getPrimary() : CtaCardItemPayload
    {
        $isInstance = $this->primary instanceof CtaCardItemPayload;

        if (! $isInstance) {
            $this->primary = new CtaCardItemPayload();
        }

        $primary = $this->primary;
        assert($primary instanceof CtaCardItemPayload);

        return $primary;
    }

    private ?CtaCardItemPayload $left = null;

    protected function setLeft(CtaCardItemPayload $left) : void
    {
        $this->left = $left;
    }

    public function getLeft() : CtaCardItemPayload
    {
        $isInstance = $this->left instanceof CtaCardItemPayload;

        if (! $isInstance) {
            $this->left = new CtaCardItemPayload();
        }

        $left = $this->left;
        assert($left instanceof CtaCardItemPayload);

        return $left;
    }

    private ?CtaCardItemPayload $right = null;

    protected function setRight(CtaCardItemPayload $right) : void
    {
        $this->right = $right;
    }

    public function getRight() : CtaCardItemPayload
    {
        $isInstance = $this->right instanceof CtaCardItemPayload;

        if (! $isInstance) {
            $this->right = new CtaCardItemPayload();
        }

        $right = $this->right;
        assert($right instanceof CtaCardItemPayload);

        return $right;
    }
}
