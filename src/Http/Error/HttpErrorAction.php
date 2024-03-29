<?php

declare(strict_types=1);

namespace App\Http\Error;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Throwable;

class HttpErrorAction
{
    private Error404Responder $error404Responder;
    private Error500Responder $error500Responder;

    public function __construct(
        Error404Responder $error404Responder,
        Error500Responder $error500Responder
    ) {
        $this->error404Responder = $error404Responder;
        $this->error500Responder = $error500Responder;
    }

    public function __invoke(
        ServerRequestInterface $request,
        Throwable $exception
    ): ResponseInterface {
        if ($exception instanceof HttpNotFoundException) {
            return ($this->error404Responder)();
        }

        return ($this->error500Responder)($exception);
    }
}
