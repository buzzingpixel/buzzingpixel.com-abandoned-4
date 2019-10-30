<?php

declare(strict_types=1);

namespace Config\Factories;

use BuzzingPixel\TwigDumper\TwigDumper;
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
    public function __invoke() : TwigEnvironment
    {
        $debug = getenv('DEV_MODE') === 'true';

        $projectPath = dirname(__DIR__, 2);

        $loader = new FilesystemLoader();

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
            $twig->addExtension(new DebugExtension());

            if (class_exists(TwigDumper::class)) {
                $twig->addExtension(new TwigDumper());
            }
        }

        return $twig;
    }
}
