<?php

declare(strict_types=1);

use App\CliServices\CliQuestionService;
use App\Content\Changelog\ParseChangelogFromMarkdownFile;
use App\Content\Meta\ExtractMetaFromPath;
use App\Content\Modules\ExtractModulesFromPath;
use App\Content\Software\ExtractSoftwareInfoFromPath;
use Config\Factories\TwigEnvironmentFactory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Twig\Environment as TwigEnvironment;
use function DI\autowire;

return [
    CliQuestionService::class => static function (ContainerInterface $di) {
        return new CliQuestionService(
            $di->get(QuestionHelper::class),
            $di->get(ArgvInput::class),
            $di->get(ConsoleOutput::class)
        );
    },
    ExtractMetaFromPath::class => autowire(ExtractMetaFromPath::class)->constructorParameter(
        'pathToContentDirectory',
        dirname(__DIR__) . '/content'
    ),
    ExtractModulesFromPath::class => autowire(ExtractModulesFromPath::class)->constructorParameter(
        'pathToContentDirectory',
        dirname(__DIR__) . '/content'
    ),
    ExtractSoftwareInfoFromPath::class => autowire(ExtractSoftwareInfoFromPath::class)->constructorParameter(
        'pathToContentDirectory',
        dirname(__DIR__) . '/content'
    ),
    ParseChangelogFromMarkdownFile::class => autowire(ParseChangelogFromMarkdownFile::class)->constructorParameter(
        'pathToContentDirectory',
        dirname(__DIR__) . '/content'
    ),
    PDO::class => static function () {
        return new PDO(
            'pgsql:host=db;port=5432;dbname=buzzingpixel',
            (string) getenv('DB_USER'),
            (string) getenv('DB_PASSWORD'),
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    },
    ResponseFactoryInterface::class => autowire(ResponseFactory::class),
    TwigEnvironment::class => static function (ContainerInterface $di) {
        return (new TwigEnvironmentFactory())($di);
    },
];
