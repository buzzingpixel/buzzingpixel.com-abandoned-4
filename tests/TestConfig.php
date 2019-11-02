<?php

declare(strict_types=1);

namespace Tests;

use Psr\Container\ContainerInterface;
use function dirname;

class TestConfig
{
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
