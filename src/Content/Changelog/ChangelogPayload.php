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
     * @param Release[] $releases
     */
    protected function setReleases(array $releases) : void
    {
        array_walk($releases, [$this, 'addRelease']);
    }

    protected function addRelease(Release $release) : void
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
