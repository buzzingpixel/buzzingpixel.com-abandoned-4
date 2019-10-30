<?php

declare(strict_types=1);

namespace Config\Factories;

use App\HttpRespose\Twig\Extensions\PhpFunctions;
use App\HttpRespose\Twig\Extensions\RequireVariables;
use BuzzingPixel\TwigDumper\TwigDumper;
use Psr\Container\ContainerInterface;
use Throwable;
use Twig\Environment as TwigEnvironment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use function class_exists;
use function dirname;
use function getenv;

class TwigEnvironmentFactory
{
    /**
     * @throws Throwable
     */
    public function __invoke(ContainerInterface $di) : TwigEnvironment
    {
        $debug = getenv('DEV_MODE') === 'true';

        $projectPath = dirname(__DIR__, 2);

        $loader = $di->get(FilesystemLoader::class);

        $loader->addPath($projectPath . '/assetsSource/templates');

        $twig = new TwigEnvironment(
            $loader,
            [
                'debug' => $debug,
                'cache' => $debug ? false : $projectPath . '/storage/twig',
                'strict_variables' => $debug,
            ]
        );

        if ($debug) {
            $twig->addExtension($di->get(DebugExtension::class));

            if (class_exists(TwigDumper::class)) {
                $twig->addExtension($di->get(TwigDumper::class));
            }
        }

        $twig->addExtension($di->get(PhpFunctions::class));

        $twig->addExtension($di->get(RequireVariables::class));

        return $twig;
    }
}