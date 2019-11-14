<?php

declare(strict_types=1);

namespace App\Content\Changelog;

use App\Payload\SpecificPayload;
use MJErwin\ParseAChangelog\Release;
use ReflectionException;
use function array_slice;
use function array_walk;

class ChangelogPayload extends SpecificPayload
{
    /** @var Release[] */
    private $releases = [];

    /**
     * @param Release[] $items
     */
    protected function setReleases(array $items) : void
    {
        array_walk($items, [$this, 'addRelease']);
    }

    private function addRelease(Release $release) : void
    {
        $this->releases[] = $release;
    }

    /**
     * @return Release[]
     */
    public function getReleases() : array
    {
        return $this->releases;
    }

    /**
     * @throws ReflectionException
     */
    public function withReleaseSlice(int $length, int $offset = 0) : ChangelogPayload
    {
        return new ChangelogPayload([
            'releases' => array_slice(
                $this->releases,
                $offset,
                $length
            ),
        ]);
    }
}
