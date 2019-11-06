<?php

declare(strict_types=1);

namespace App\Http\Software;

use App\Content\Meta\ExtractMetaFromPath;
use App\Content\Modules\ExtractModulesFromPath;
use App\Content\Software\ExtractSoftwareInfoFromPath;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class GetSoftwareAction
{
    private const PATH_MAP = [
        '/software/ansel-craft' => 'Software/AnselCraft',
        '/software/ansel-ee' => 'Software/AnselEE',
    ];

    /** @var GetSoftwareResponder */
    private $responder;
    /** @var ExtractMetaFromPath */
    private $extractMetaFromPath;
    /** @var ExtractModulesFromPath */
    private $extractModulesFromPath;
    /** @var ExtractSoftwareInfoFromPath */
    private $extractSoftwareInfoFromPath;

    public function __construct(
        GetSoftwareResponder $responder,
        ExtractMetaFromPath $extractMetaFromPath,
        ExtractModulesFromPath $extractModulesFromPath,
        ExtractSoftwareInfoFromPath $extractSoftwareInfoFromPath
    ) {
        $this->responder                   = $responder;
        $this->extractMetaFromPath         = $extractMetaFromPath;
        $this->extractModulesFromPath      = $extractModulesFromPath;
        $this->extractSoftwareInfoFromPath = $extractSoftwareInfoFromPath;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(ServerRequestInterface $request) : ResponseInterface
    {
        $uriPath = $request->getUri()->getPath();

        $contentPath = self::PATH_MAP[$uriPath];

        return ($this->responder)(
            ($this->extractMetaFromPath)($contentPath),
            ($this->extractModulesFromPath)($contentPath),
            ($this->extractSoftwareInfoFromPath)($contentPath),
            $uriPath
        );
    }
}
