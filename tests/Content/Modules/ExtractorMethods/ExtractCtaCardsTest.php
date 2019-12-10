<?php

declare(strict_types=1);

namespace Tests\Content\Modules\ExtractorMethods;

use PHPUnit\Framework\TestCase;
use Tests\TestConfig;
use Throwable;

class ExtractCtaCardsTest extends TestCase
{
    /** @var ExtractCtaCardsImplementation */
    private $extractor;

    protected function setUp() : void
    {
        $this->extractor = TestConfig::$di->get(ExtractCtaCardsImplementation::class);
    }

    /**
     * @throws Throwable
     */
    public function testEmptyArrayInput() : void
    {
        $payload = $this->extractor->runTest([]);

        $primary = $payload->getPrimary();
        self::assertSame('', $primary->getHeading());
        self::assertSame('', $primary->getContent());
        self::assertSame([], $primary->getTextBullets());
        self::assertSame([], $primary->getCtas());
        self::assertSame('', $primary->getFooterContent());

        $left = $payload->getLeft();
        self::assertSame('', $left->getHeading());
        self::assertSame('', $left->getContent());
        self::assertSame([], $left->getTextBullets());
        self::assertSame([], $left->getCtas());
        self::assertSame('', $left->getFooterContent());

        $right = $payload->getRight();
        self::assertSame('', $right->getHeading());
        self::assertSame('', $right->getContent());
        self::assertSame([], $right->getTextBullets());
        self::assertSame([], $right->getCtas());
        self::assertSame('', $right->getFooterContent());
    }

    /**
     * @throws Throwable
     */
    public function testCtasAndTextBulletsNotArray() : void
    {
        $payload = $this->extractor->runTest([
            'primary' => [
                'bullets' => 'bar',
                'ctas' => 'foo',
            ],
        ]);

        $primary = $payload->getPrimary();
        self::assertSame('', $primary->getHeading());
        self::assertSame('', $primary->getContent());
        self::assertSame([], $primary->getTextBullets());
        self::assertSame([], $primary->getCtas());
        self::assertSame('', $primary->getFooterContent());

        $left = $payload->getLeft();
        self::assertSame('', $left->getHeading());
        self::assertSame('', $left->getContent());
        self::assertSame([], $left->getTextBullets());
        self::assertSame([], $left->getCtas());
        self::assertSame('', $left->getFooterContent());

        $right = $payload->getRight();
        self::assertSame('', $right->getHeading());
        self::assertSame('', $right->getContent());
        self::assertSame([], $right->getTextBullets());
        self::assertSame([], $right->getCtas());
        self::assertSame('', $right->getFooterContent());
    }

    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $payload = $this->extractor->runTest([
            'primary' => [
                'heading' => 123,
                'content' => 456.78,
                'bullets' => [
                    'foo',
                    'bar',
                    'baz',
                ],
                'ctas' => [
                    [],
                    [
                        'foo',
                        'baz',
                    ],
                    ['content' => 'FooBar'],
                    ['href' => 'BarBaz'],
                ],
                'footerContent' => [],
            ],
            'right' => [
                'heading' => 'RightHeading',
                'content' => 'RightContent',
                'bullets' => ['barBaz'],
                'ctas' => [
                    [
                        'content' => 'FooBar',
                        'href' => 'BazFoo',
                    ],
                ],
                'footerContent' => 'RightFooterContent',
            ],
            'left' => [
                'heading' => 'LeftHeading',
                'content' => 'LeftContent',
            ],
        ]);

        $primary = $payload->getPrimary();
        self::assertSame('123', $primary->getHeading());
        self::assertSame("<p>456.78</p>\n", $primary->getContent());
        self::assertSame(
            [
                'foo',
                'bar',
                'baz',
            ],
            $primary->getTextBullets()
        );
        self::assertSame('Array', $primary->getFooterContent());
        $primaryCtas = $primary->getCtas();
        self::assertCount(4, $primaryCtas);
        $primaryCta0 = $primaryCtas[0];
        self::assertSame('', $primaryCta0->getHref());
        self::assertSame('', $primaryCta0->getContent());
        $primaryCta1 = $primaryCtas[1];
        self::assertSame('', $primaryCta1->getHref());
        self::assertSame('', $primaryCta1->getContent());
        $primaryCta2 = $primaryCtas[2];
        self::assertSame('', $primaryCta2->getHref());
        self::assertSame('FooBar', $primaryCta2->getContent());
        $primaryCta3 = $primaryCtas[3];
        self::assertSame('BarBaz', $primaryCta3->getHref());
        self::assertSame('', $primaryCta3->getContent());

        $left = $payload->getLeft();
        self::assertSame('LeftHeading', $left->getHeading());
        self::assertSame("<p>LeftContent</p>\n", $left->getContent());
        self::assertSame([], $left->getTextBullets());
        self::assertSame([], $left->getCtas());
        self::assertSame('', $left->getFooterContent());

        $right = $payload->getRight();
        self::assertSame('RightHeading', $right->getHeading());
        self::assertSame("<p>RightContent</p>\n", $right->getContent());
        self::assertSame(['barBaz'], $right->getTextBullets());
        self::assertSame('RightFooterContent', $right->getFooterContent());
        $rightCtas = $right->getCtas();
        self::assertCount(1, $rightCtas);
        $rightCta0 = $rightCtas[0];
        self::assertSame('BazFoo', $rightCta0->getHref());
        self::assertSame('FooBar', $rightCta0->getContent());
    }
}
