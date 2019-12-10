<?php

declare(strict_types=1);

namespace Tests\Http\Software;

use App\Content\Changelog\ChangelogPayload;
use App\Content\Meta\MetaPayload;
use App\Content\Software\SoftwareInfoPayload;
use App\Http\Software\GetChangelogResponder;
use App\HttpHelpers\Pagination\Pagination;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ResponseFactory;
use Tests\TestConfig;
use Throwable;
use Twig\Environment;

class GetChangelogResponderTest extends TestCase
{
    /** @var GetChangelogResponder */
    private $responder;

    /** @var MetaPayload */
    private $metaPayload;
    /** @var ChangelogPayload */
    private $allChangelogPayload;
    /** @var ChangelogPayload */
    private $changelogPayload;
    /** @var Pagination */
    private $pagination;
    /** @var SoftwareInfoPayload */
    private $softwareInfoPayload;

    /** @var string */
    private $uriPath = '/test/uri/path';

    /** @var MockObject&Environment */
    private $twigEnvironment;

    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $response = ($this->responder)(
            $this->metaPayload,
            $this->allChangelogPayload,
            $this->changelogPayload,
            $this->pagination,
            $this->softwareInfoPayload,
            $this->uriPath
        );

        self::assertSame(200, $response->getStatusCode());

        self::assertSame(
            'TwigRenderOutputTest',
            $response->getBody()->__toString()
        );
    }

    /**
     * @throws Throwable
     */
    protected function setUp() : void
    {
        $this->metaPayload         = new MetaPayload();
        $this->allChangelogPayload = new ChangelogPayload();
        $this->changelogPayload    = new ChangelogPayload();
        $this->pagination          = new Pagination();
        $this->softwareInfoPayload = new SoftwareInfoPayload();

        $this->mockTwigEnvironment();

        $this->responder = new GetChangelogResponder(
            TestConfig::$di->get(ResponseFactory::class),
            $this->twigEnvironment,
        );
    }

    private function mockTwigEnvironment() : void
    {
        $this->twigEnvironment = $this->createMock(Environment::class);

        $this->twigEnvironment->expects(self::once())
            ->method('render')
            ->with(
                self::equalTo('SoftwareChangelogPage.twig'),
                self::equalTo([
                    'metaPayload' => $this->metaPayload,
                    'allChangelogPayload' => $this->allChangelogPayload,
                    'changelogPayload' => $this->changelogPayload,
                    'pagination' => $this->pagination,
                    'softwareInfoPayload' => $this->softwareInfoPayload,
                    'uriPath' => $this->uriPath,
                    'activeHref' => $this->uriPath . '/changelog',
                ])
            )
            ->willReturn('TwigRenderOutputTest');
    }
}
