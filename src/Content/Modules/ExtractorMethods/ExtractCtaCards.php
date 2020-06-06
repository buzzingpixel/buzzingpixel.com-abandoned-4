<?php

declare(strict_types=1);

namespace App\Content\Modules\ExtractorMethods;

use App\Content\Modules\Payloads\CtaCardItemPayload;
use App\Content\Modules\Payloads\CtaCardsPayload;
use cebe\markdown\GithubMarkdown;
use Throwable;
use function array_map;
use function is_array;
use function is_string;

/**
 * Requires parent to have:
 * `use \App\Content\Modules\CommonTraits\MapYamlCtaToPayload;`
 *
 * Requires $markdownParser property on parent
 *
 * @property GithubMarkdown $markdownParser
 */
trait ExtractCtaCards
{
    /**
     * @param array<string, mixed> $parsedYaml
     *
     * @throws Throwable
     */
    protected function extractCtaCards(array $parsedYaml) : CtaCardsPayload
    {
        /** @var array<string, mixed> $primary */
        $primary = isset($parsedYaml['primary']) && is_array($parsedYaml['primary']) ?
            $parsedYaml['primary'] :
            [];

        /** @var array<string, mixed> $left */
        $left = isset($parsedYaml['left']) && is_array($parsedYaml['left']) ?
            $parsedYaml['left'] :
            [];

        /** @var array<string, mixed> $right */
        $right = isset($parsedYaml['right']) && is_array($parsedYaml['right']) ?
            $parsedYaml['right'] :
            [];

        return new CtaCardsPayload([
            'preHeadline' => (string) ($parsedYaml['preHeadline'] ?? ''),
            'headline' => (string) ($parsedYaml['headline'] ?? ''),
            'content' => (string) ($parsedYaml['content'] ?? ''),
            'primary' => $this->mapCtaCardInnerYamlToItemPayload($primary),
            'left' => $this->mapCtaCardInnerYamlToItemPayload($left),
            'right' => $this->mapCtaCardInnerYamlToItemPayload($right),
        ]);
    }

    /**
     * @param array<string, mixed> $parsedYaml
     *
     * @throws Throwable
     */
    private function mapCtaCardInnerYamlToItemPayload(array $parsedYaml) : CtaCardItemPayload
    {
        $textBullets = isset($parsedYaml['bullets']) && is_array($parsedYaml['bullets']) ?
            $parsedYaml['bullets'] :
            [];

        $yamlCtas = isset($parsedYaml['ctas']) && is_array($parsedYaml['ctas']) ?
            $parsedYaml['ctas'] :
            [];

        /** @psalm-suppress MixedAssignment */
        $heading = $parsedYaml['heading'] ?? '';
        /** @psalm-suppress MixedAssignment */
        $content = $parsedYaml['content'] ?? '';
        /** @psalm-suppress MixedAssignment */
        $footerContent = $parsedYaml['footerContent'] ?? '';

        return new CtaCardItemPayload([
            'heading' => is_string($heading) ? $heading : '',
            'content' => $this->markdownParser->parse(
                is_string($content) ? $content : ''
            ),
            'textBullets' => $textBullets,
            'ctas' => array_map([$this, 'mapYamlCtaToPayload'], $yamlCtas),
            'footerContent' => $this->markdownParser->parseParagraph(
                is_string($footerContent) ? $footerContent : ''
            ),
        ]);
    }
}
