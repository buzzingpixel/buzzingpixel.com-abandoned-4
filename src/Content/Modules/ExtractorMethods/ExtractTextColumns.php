<?php

declare(strict_types=1);

namespace App\Content\Modules\ExtractorMethods;

use App\Content\Modules\Payloads\ContentWithHeadingPayload;
use App\Content\Modules\Payloads\TextColumnsPayload;
use cebe\markdown\GithubMarkdown;
use Throwable;
use function array_map;
use function is_array;

/**
 * Requires $markdownParser property on parent
 *
 * @property GithubMarkdown $markdownParser
 */
trait ExtractTextColumns
{
    /**
     * @param array<string, mixed> $parsedYaml
     *
     * @throws Throwable
     */
    protected function extractTextColumns(array $parsedYaml) : TextColumnsPayload
    {
        /** @var array<int, array<string, string>> $columns */
        $columns = isset($parsedYaml['columns']) && is_array($parsedYaml['columns']) ?
            $parsedYaml['columns'] :
            [];

        return new TextColumnsPayload([
            'backgroundColor' => (string) ($parsedYaml['backgroundColor'] ?? ''),
            'items' => array_map(
                [$this, 'mapTextColumnArrayToPayload'],
                $columns
            ),
        ]);
    }

    /**
     * @param array<string, string> $textColumn
     *
     * @throws Throwable
     */
    private function mapTextColumnArrayToPayload(array $textColumn) : ContentWithHeadingPayload
    {
        return new ContentWithHeadingPayload([
            'headline' => (string) ($textColumn['headline'] ?? ''),
            'content' => $this->markdownParser->parse(
                (string) ($textColumn['content'] ?? '')
            ),
        ]);
    }
}
