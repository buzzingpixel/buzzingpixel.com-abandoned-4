<?php

declare(strict_types=1);

namespace Tests\Http\Cart;

use App\Cart\Models\CartModel;
use App\Content\Meta\MetaPayload;
use App\Http\Cart\GetCartResponder;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Tests\TestConfig;
use Throwable;
use Twig\Environment as TwigEnvironment;
use function assert;

class GetCartResponderTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $cart = new CartModel();

        $twigEnvironment = $this->createMock(
            TwigEnvironment::class
        );

        $twigEnvironment->expects(self::once())
            ->method('render')
            ->willReturnCallback(
                static function (
                    string $template,
                    array $context
                ) use (
                    $cart
                ) {
                    self::assertSame('Http/Cart.twig', $template);

                    self::assertCount(2, $context);

                    $metaPayload = $context['metaPayload'];

                    assert($metaPayload instanceof MetaPayload);

                    self::assertSame(
                        'Your Cart',
                        $metaPayload->getMetaTitle(),
                    );

                    self::assertSame($cart, $context['cart']);

                    return 'TwigReturnString';
                }
            );

        $responder = new GetCartResponder(
            TestConfig::$di->get(ResponseFactoryInterface::class),
            $twigEnvironment,
        );

        $response = $responder($cart);

        self::assertSame(
            'TwigReturnString',
            $response->getBody()->__toString(),
        );
    }
}
