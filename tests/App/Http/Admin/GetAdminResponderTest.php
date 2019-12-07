<?php

declare(strict_types=1);

namespace Tests\App\Http\Admin;

use App\Content\Meta\MetaPayload;
use App\Http\Admin\GetAdminResponder;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use stdClass;
use Tests\TestConfig;
use Throwable;
use Twig\Environment;
use function func_get_args;

class GetAdminResponderTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $twigRenderArgHolder = new stdClass();

        $twigRenderArgHolder->args = [];

        $twig = $this->createMock(Environment::class);

        $twig->expects(self::once())
            ->method('render')
            ->willReturnCallback(
                static function () use ($twigRenderArgHolder) {
                    $twigRenderArgHolder->args = func_get_args();

                    return 'FooTwigOutput';
                }
            );

        $responder = new GetAdminResponder(
            TestConfig::$di->get(
                ResponseFactoryInterface::class
            ),
            $twig,
        );

        $response = $responder(
            'Foo/Template.twig',
            'Foo Title',
            'foo-tab'
        );

        self::assertSame(200, $response->getStatusCode());

        self::assertSame(
            'FooTwigOutput',
            $response->getBody()->__toString()
        );

        /** @var mixed[] $args */
        $args = $twigRenderArgHolder->args;

        self::assertCount(2, $args);

        self::assertSame(
            'Foo/Template.twig',
            $args[0]
        );

        /** @var mixed[] $context */
        $context = $args[1];

        self::assertCount(2, $context);

        /** @var MetaPayload|null $metaPayload */
        $metaPayload = $context['metaPayload'];

        self::assertInstanceOf(
            MetaPayload::class,
            $metaPayload
        );

        self::assertSame(
            'Foo Title',
            $metaPayload->getMetaTitle()
        );

        self::assertSame('foo-tab', $context['activeTab']);
    }
}
