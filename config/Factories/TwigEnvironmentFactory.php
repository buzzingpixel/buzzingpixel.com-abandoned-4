<?php

declare(strict_types=1);

namespace Config\Factories;

use App\HttpResponse\Twig\Extensions\Countries;
use App\HttpResponse\Twig\Extensions\FetchLoggedInUser;
use App\HttpResponse\Twig\Extensions\PhpFunctions;
use App\HttpResponse\Twig\Extensions\ReadJson;
use App\HttpResponse\Twig\Extensions\RequireVariables;
use App\HttpResponse\Twig\Extensions\Slugify;
use App\HttpResponse\Twig\Extensions\TemplateExists;
use App\HttpResponse\Twig\Extensions\TimeZoneList;
use BuzzingPixel\TwigDumper\TwigDumper;
use buzzingpixel\twiggetenv\GetEnvTwigExtension;
use BuzzingPixel\TwigMarkdown\MarkdownTwigExtension;
use buzzingpixel\twigsmartypants\SmartypantsTwigExtension;
use buzzingpixel\twigswitch\SwitchTwigExtension;
use buzzingpixel\twigwidont\WidontTwigExtension;
use Config\Footer;
use Config\General;
use Config\MainMenu;
use Knlv\Slim\Views\TwigMessages;
use Psr\Container\ContainerInterface;
use Slim\Csrf\Guard as Csrf;
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

        $loader->addPath($projectPath . '/assets/templates');

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

        $twig->addExtension($di->get(SmartypantsTwigExtension::class));

        $twig->addExtension($di->get(WidontTwigExtension::class));

        $twig->addExtension($di->get(SwitchTwigExtension::class));

        $twig->addExtension($di->get(GetEnvTwigExtension::class));

        $twig->addExtension(new TemplateExists($twig->getLoader()));

        $twig->addExtension($di->get(Slugify::class));

        $twig->addExtension($di->get(MarkdownTwigExtension::class));

        $twig->addExtension($di->get(TwigMessages::class));

        $twig->addExtension($di->get(FetchLoggedInUser::class));

        $twig->addExtension($di->get(Countries::class));

        $twig->addExtension($di->get(TimeZoneList::class));

        $twig->addExtension($di->get(ReadJson::class));

        $twig->addGlobal('GeneralConfig', $di->get(General::class));

        $twig->addGlobal('MainMenu', $di->get(MainMenu::class));

        $twig->addGlobal('Footer', $di->get(Footer::class));

        $twig->addGlobal('csrf', $di->get(Csrf::class));

        return $twig;
    }
}
