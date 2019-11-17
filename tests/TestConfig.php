<?php

declare(strict_types=1);

namespace Tests;

use Psr\Container\ContainerInterface;
use function dirname;

class TestConfig
{
    public const TESTS_BASE_PATH = __DIR__;

    /** @var ContainerInterface */
    public static $di;

    public function __construct()
    {
        if (static::$di) {
            return;
        }

        $bootstrap = include dirname(__DIR__) . '/config/bootstrap.php';

        static::$di = $bootstrap();
    }
}
