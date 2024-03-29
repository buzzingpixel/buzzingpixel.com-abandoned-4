<?php

declare(strict_types=1);

namespace App\StaticCache\Services;

use Config\General;

use function exec;

/**
 * Ignored from test coverage because of the exec command
 */
class ClearStaticCache
{
    private General $generalConfig;

    public function __construct(General $generalConfig)
    {
        $this->generalConfig = $generalConfig;
    }

    public function __invoke(): void
    {
        $storagePath = $this->generalConfig->pathToStorageDirectory();

        $staticCachePath = $storagePath . '/static-cache/*';

        exec('rm -rf ' . $staticCachePath);
    }
}
