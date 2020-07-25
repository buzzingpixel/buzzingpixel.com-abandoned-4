<?php

declare(strict_types=1);

namespace Tests\Http\Software;

use App\Http\Software\GetChangelogRawAction;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Tests\TestConfig;
use Throwable;

class GetChangelogRawActionTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testWhenChangelogInternal(): void
    {
        $action = TestConfig::$di->get(GetChangelogRawAction::class);

        $response = ($action)(
            $this->mockRequest(
                '/software/ansel-ee/changelog/raw'
            )
        );

        self::assertSame(
            [
                'EnableStaticCache' => ['true'],
                'Content-Type' => ['text/plain'],
            ],
            $response->getHeaders()
        );

        self::assertNotEmpty($response->getBody()->__toString());
    }

    /**
     * @throws Throwable
     */
    public function testWhenChangelogExternal(): void
    {
        $action = TestConfig::$di->get(GetChangelogRawAction::class);

        $response = ($action)(
            $this->mockRequest(
                '/software/ansel-craft/changelog/raw'
            )
        );

        self::assertSame(
            [
                'EnableStaticCache' => ['true'],
                'Content-Type' => ['text/plain'],
            ],
            $response->getHeaders()
        );

        self::assertNotEmpty($response->getBody()->__toString());
    }

    private function mockRequest(string $uriString): ServerRequestInterface
    {
        $uri = $this->createMock(UriInterface::class);

        $uri->method('getPath')->willReturn($uriString);

        $mock = $this->createMock(
            ServerRequestInterface::class
        );

        $mock->method('getUri')->willReturn($uri);

        return $mock;
    }
}
