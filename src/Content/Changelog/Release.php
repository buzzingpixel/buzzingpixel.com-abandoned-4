<?php

declare(strict_types=1);

namespace App\Content\Changelog;

use MJErwin\ParseAChangelog\Release as ErwinRelease;

class Release extends ErwinRelease
{
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
