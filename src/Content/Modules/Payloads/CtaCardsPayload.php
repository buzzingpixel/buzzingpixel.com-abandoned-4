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
        $isInstance = $this->primary instanceof CtaCardItemPayload;

        if (! $isInstance) {
            $this->primary = new CtaCardItemPayload();
        }

        // We have to do this to make PHPStan happy because it doesn't
        // understand that the condition above makes sure we always send
        // an instance of CtaCardItemPayload
        /** @var CtaCardItemPayload $primary */
        $primary = $this->primary;

        return $primary;
    }

    /** @var CtaCardItemPayload|null */
    private $left;

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

        // We have to do this to make PHPStan happy because it doesn't
        // understand that the condition above makes sure we always send
        // an instance of CtaCardItemPayload
        /** @var CtaCardItemPayload $left */
        $left = $this->left;

        return $left;
    }

    /** @var CtaCardItemPayload|null */
    private $right;

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

        // We have to do this to make PHPStan happy because it doesn't
        // understand that the condition above makes sure we always send
        // an instance of CtaCardItemPayload
        /** @var CtaCardItemPayload $right */
        $right = $this->right;

        return $right;
    }
}
