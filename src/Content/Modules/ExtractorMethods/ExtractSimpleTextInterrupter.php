<?php

declare(strict_types=1);

namespace App\Content\Modules\ExtractorMethods;

use App\Content\Modules\Payloads\SimpleTextInterrupterPayload;
use Throwable;

trait ExtractSimpleTextInterrupter
{
    /**
     * @param array<string, mixed> $parsedYaml
     *
     * @throws Throwable
     */
    protected function extractSimpleTextInterrupter(
        array $parsedYaml
    ): SimpleTextInterrupterPayload {
        return new SimpleTextInterrupterPayload([
            'backgroundColor' => (string) ($parsedYaml['backgroundColor'] ?? ''),
            'textColor' => (string) ($parsedYaml['textColor'] ?? ''),
            'headline' => (string) ($parsedYaml['headline'] ?? ''),
            'subHeadline' => (string) ($parsedYaml['subHeadline'] ?? ''),
            'isH1' => (bool) ($parsedYaml['isH1'] ?? false),
        ]);
    }
}
