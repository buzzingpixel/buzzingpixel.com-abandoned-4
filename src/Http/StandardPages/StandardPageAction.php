<?php

declare(strict_types=1);

namespace App\Http\StandardPages;

use App\Content\Meta\ExtractMetaFromPath;
use App\Content\Modules\ExtractModulesFromPath;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Throwable;

class StandardPageAction
{
    /** @var string[] */
    private static array $contentPathMap = [
        '/' => 'HomePage',
        '/terms' => 'Terms',
        '/privacy' => 'Privacy',
        '/cookies' => 'Cookies',
    ];

    private StandardPageResponder $responder;
    private ExtractMetaFromPath $extractMetaFromPath;
    private ExtractModulesFromPath $extractModulesFromPath;

    public function __construct(
        StandardPageResponder $responder,
        ExtractMetaFromPath $extractMetaFromPath,
        ExtractModulesFromPath $extractModulesFromPath
    ) {
        $this->responder              = $responder;
        $this->extractMetaFromPath    = $extractMetaFromPath;
        $this->extractModulesFromPath = $extractModulesFromPath;
    }

    /**
     * @throws Throwable
     * @throws HttpNotFoundException
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $path = self::$contentPathMap[$request->getUri()->getPath()] ?? null;

        if ($path === null) {
            throw new HttpNotFoundException($request);
        }

        return ($this->responder)(
            ($this->extractMetaFromPath)($path),
            ($this->extractModulesFromPath)($path)
        );
    }
}
