<?php

declare(strict_types=1);

namespace App\Content\Changelog;

use MJErwin\ParseAChangelog\Reader;
use Throwable;
use const FILTER_VALIDATE_URL;
use function filter_var;

class ParseChangelogFromMarkdownFile
{
    /** @var string */
    private $pathToContentDirectory;

    public function __construct(string $pathToContentDirectory)
    {
        $this->pathToContentDirectory = $pathToContentDirectory;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(string $location) : ChangelogPayload
    {
        if (! filter_var($location, FILTER_VALIDATE_URL)) {
            $location = $this->pathToContentDirectory . '/' . $location;
        }

        try {
            return $this->readFromLocation($location);
        } catch (Throwable $e) {
            return new ChangelogPayload();
        }
    }

    /**
     * @throws Throwable
     */
    private function readFromLocation(string $location) : ChangelogPayload
    {
        $reader = new Reader($location);

        return new ChangelogPayload(['releases' => $reader->getReleases()]);
    }
}
