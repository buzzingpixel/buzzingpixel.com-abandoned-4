<?php

declare(strict_types=1);

namespace App\Http\Error;

use App\Content\Meta\MetaPayload;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Twig\Environment as TwigEnvironment;

class Error404Responder
{
    private ResponseFactoryInterface $responseFactory;
    private TwigEnvironment $twigEnvironment;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        TwigEnvironment $twigEnvironment
    ) {
        $this->responseFactory = $responseFactory;
        $this->twigEnvironment = $twigEnvironment;
    }

    public function __invoke(): ResponseInterface
    {
        $response = $this->responseFactory->createResponse(
            404,
            'Page not found'
        )
            // We'll statically cache the response so 404s can't DDOS us
            ->withHeader('EnableStaticCache', 'true');

        $response->getBody()->write($this->twigEnvironment->render(
            'Http/Errors/404.twig',
            [
                'metaPayload' => new MetaPayload(
                    ['metaTitle' => 'Page not found']
                ),
            ]
        ));

        return $response;
    }
}
