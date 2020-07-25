<?php

declare(strict_types=1);

namespace Tests\Http\Error;

use App\Http\Error\Error404Responder;
use App\Http\Error\Error500Responder;
use App\Http\Error\HttpErrorAction;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Factory\ResponseFactory;

class HttpErrorActionTest extends TestCase
{
    private HttpErrorAction $action;

    /** @var MockObject&ServerRequestInterface */
    private $request;

    /** @var MockObject&LoggerInterface */
    private $logger;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(
            LoggerInterface::class
        );

        $responseFactory = new ResponseFactory();

        $this->action = new HttpErrorAction(
            new Error404Responder(
                $responseFactory
            ),
            new Error500Responder(
                $responseFactory,
                $this->logger
            )
        );

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $this->request = $request;
    }

    public function testError404(): void
    {
        $exception = new HttpNotFoundException($this->request);

        $this->logger->expects(self::never())
            ->method(self::anything());

        $response = ($this->action)(
            $this->request,
            $exception
        );

        self::assertSame(404, $response->getStatusCode());

        self::assertSame(
            ['EnableStaticCache' => ['true']],
            $response->getHeaders()
        );

        self::assertSame(
            'Page not found',
            $response->getReasonPhrase()
        );

        self::assertSame(
            'Page not found',
            (string) $response->getBody()
        );
    }

    public function testError500(): void
    {
        $exception = new Exception();

        $this->logger->expects(self::once())
            ->method('error')
            ->with(
                self::equalTo('An exception was thrown'),
                self::equalTo(['exception' => $exception]),
            );

        $response = ($this->action)(
            $this->request,
            $exception
        );

        self::assertSame(
            500,
            $response->getStatusCode()
        );

        self::assertSame(
            ['EnableStaticCache' => ['true']],
            $response->getHeaders()
        );

        self::assertSame(
            'An internal server error occurred',
            $response->getReasonPhrase()
        );

        self::assertSame(
            'An internal server error occurred',
            (string) $response->getBody()
        );
    }
}
