<?php

declare(strict_types=1);

namespace App\Content\Modules\CommonTraits;

use App\Content\Modules\Payloads\CtaPayload;
use Throwable;

trait MapYamlCtaToPayload
{
    /**
     * @param array<string, mixed> $yamlCta
     *
     * @throws Throwable
     */
    protected function mapYamlCtaToPayload(array $yamlCta) : CtaPayload
    {
        return new CtaPayload([
            'href' => (string) ($yamlCta['href'] ?? ''),
            'content' => (string) ($yamlCta['content'] ?? ''),
        ]);
    }
}
