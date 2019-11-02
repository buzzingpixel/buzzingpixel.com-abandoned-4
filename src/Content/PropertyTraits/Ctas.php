<?php

declare(strict_types=1);

namespace App\Content\PropertyTraits;

use App\Content\Modules\Payloads\CtaPayload;
use InvalidArgumentException;

trait Ctas
{
    /** @var CtaPayload[] */
    private $ctas = [];

    /**
     * @param CtaPayload[] $ctas
     */
    protected function setCtas(array $ctas) : void
    {
        foreach ($ctas as $cta) {
            if ($cta instanceof CtaPayload) {
                continue;
            }

            throw new InvalidArgumentException(
                'Cta must be instance of ' . CtaPayload::class
            );
        }

        $this->ctas = $ctas;
    }

    /**
     * @return CtaPayload[]
     */
    public function getCtas() : array
    {
        return $this->ctas;
    }
}
