<?php

declare(strict_types=1);

use App\Content\Meta\ExtractMetaFromPath;
use App\Content\Modules\ExtractModulesFromPath;
use Config\Factories\TwigEnvironmentFactory;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Twig\Environment as TwigEnvironment;
use function DI\autowire;

return [
    ExtractMetaFromPath::class => autowire(ExtractMetaFromPath::class)->constructorParameter(
        'pathToContentDirectory',
        dirname(__DIR__) . '/content'
    ),
    ExtractModulesFromPath::class => autowire(ExtractModulesFromPath::class)->constructorParameter(
        'pathToContentDirectory',
        dirname(__DIR__) . '/content'
    ),
    ResponseFactoryInterface::class => autowire(ResponseFactory::class),
    TwigEnvironment::class => static function () {
        return (new TwigEnvironmentFactory())();
    },
];
