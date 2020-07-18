<?php

declare(strict_types=1);

namespace App\Content\PropertyTraits;

use App\Content\Modules\Payloads\CtaPayload;

use function array_walk;

trait Ctas
{
    /** @var CtaPayload[] */
    private array $ctas = [];

    /**
     * @param CtaPayload[] $ctas
     */
    protected function setCtas(array $ctas): void
    {
        array_walk($ctas, [$this, 'addCta']);
    }

    private function addCta(CtaPayload $cta): void
    {
        $this->ctas[] = $cta;
    }

    /**
     * @return CtaPayload[]
     */
    public function getCtas(): array
    {
        return $this->ctas;
    }
}
