<?php

declare(strict_types=1);

namespace App\Content\Changelog;

use MJErwin\ParseAChangelog\Release as ErwinRelease;
use function in_array;

class Release extends ErwinRelease
{
    public const MESSAGE_TYPES = [
        'added',
        'changed',
        'deprecated',
        'removed',
        'fixed',
        'security',
    ];

    /**
     * @return array<array<string>>
     */
    public function getMessageTypesContent() : array
    {
        $arr = [];

        foreach ($this->toArray() as $key => $item) {
            if (! in_array($key, self::MESSAGE_TYPES) ||
                empty($item)
            ) {
                continue;
            }

            $arr[$key] = $item;
        }

        return $arr;
    }

    /**
     * Decided not to do this for now
     */
    // public function toHtml() : string
    // {
    //     $contentString = implode("\n", $this->content);
    //
    //     // Oh man I hate this
    //     $parser = new GithubMarkdown();
    //
    //     $parser->enableNewlines = true;
    //     $parser->html5          = true;
    //
    //     return $parser->parse($contentString);
    // }
}
