<?php

declare(strict_types=1);

namespace Tests\App\Http\Error;

use App\Http\Error\Error404Responder;
use App\Http\Error\Error500Responder;
use App\Http\Error\HttpErrorAction;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Factory\ResponseFactory;

class HttpErrorActionTest extends TestCase
{
    /** @var HttpErrorAction */
    private $action;

    /** @var MockObject&ServerRequestInterface */
    private $request;

    protected function setUp() : void
    {
        $responseFactory = new ResponseFactory();

        $this->action = new HttpErrorAction(
            new Error404Responder($responseFactory),
            new Error500Responder($responseFactory)
        );

        $request = $this->createMock(ServerRequestInterface::class);

        $this->request = $request;
    }

    public function testError404() : void
    {
        $exception = new HttpNotFoundException($this->request);

        $response = ($this->action)($this->request, $exception);

        self::assertSame(404, $response->getStatusCode());

        self::assertSame('Page not found', $response->getReasonPhrase());

        self::assertSame('Page not found', (string) $response->getBody());
    }

    public function testError500() : void
    {
        $exception = new Exception();

        $response = ($this->action)($this->request, $exception);

        self::assertSame(500, $response->getStatusCode());

        self::assertSame('An internal server error occurred', $response->getReasonPhrase());

        self::assertSame('An internal server error occurred', (string) $response->getBody());
    }
}
