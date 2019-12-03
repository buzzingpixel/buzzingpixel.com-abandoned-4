<?php

declare(strict_types=1);

namespace Tests;

use Di\Container;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Csrf\Guard as CsrfGuard;
use Slim\Flash\Messages;
use function dirname;

class TestConfig
{
    public const TESTS_BASE_PATH = __DIR__;

    /** @var ContainerInterface */
    public static $di;

    /** @var mixed[] */
    public static $flashStorage = [];

    /** @var mixed[] */
    public static $csrfStorage = [];

    public function __construct()
    {
        if (static::$di) {
            return;
        }

        $bootstrap = include dirname(__DIR__) . '/config/bootstrap.php';

        /** @var Container $di */
        $di = $bootstrap();

        $di->set(
            Messages::class,
            static function () : Messages {
                return new Messages(self::$flashStorage);
            }
        );

        $di->set(
            CsrfGuard::class,
            static function (ContainerInterface $di) : CsrfGuard {
                return new CsrfGuard(
                    $di->get(ResponseFactoryInterface::class),
                    'csrf',
                    self::$csrfStorage
                );
            }
        );

        static::$di = $di;
    }
}
