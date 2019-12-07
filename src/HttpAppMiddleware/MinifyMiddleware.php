<?php

declare(strict_types=1);

namespace App\HttpAppMiddleware;

use App\Factories\StreamFactory;
use App\HttpResponse\Minifier;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Safe\Exceptions\FilesystemException;
use function trim;

class MinifyMiddleware
{
    /** @var Minifier */
    private $minifier;
    /** @var StreamFactory */
    private $streamFactory;

    public function __construct(
        Minifier $minifier,
        StreamFactory $streamFactory
    ) {
        $this->minifier      = $minifier;
        $this->streamFactory = $streamFactory;
    }

    /**
     * @throws FilesystemException
     */
    public function __invoke(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ) : ResponseInterface {
        $response = $handler->handle($request);

        $contentType = $response->getHeader('Content-Type');

        $contentTypeString = $contentType[0] ?? 'text/html';

        if ($contentTypeString !== 'text/html') {
            return $response;
        }

        $content = (string) $response->getBody();

        if (trim($content) === '') {
            return $response;
        }

        $body = $this->streamFactory->make();

        $body->write(($this->minifier)($content));

        return $response->withBody($body);
    }
}
