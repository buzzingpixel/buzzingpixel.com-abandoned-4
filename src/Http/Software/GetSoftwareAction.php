<?php

declare(strict_types=1);

namespace App\Http\Software;

use App\Content\Meta\ExtractMetaFromPath;
use App\Content\Modules\ExtractModulesFromPath;
use App\Content\Modules\ModulePayload;
use App\Content\Modules\Payloads\CtasPayload;
use App\Content\Software\ExtractSoftwareInfoFromPath;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class GetSoftwareAction
{
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

        $contentPath = PathMap::PATH_MAP[$uriPath];

        $softwareInfoPayload = ($this->extractSoftwareInfoFromPath)($contentPath);

        $modulePayload = ($this->extractModulesFromPath)($contentPath);

        $modulePayloadItems = $modulePayload->getItems();

        $modulePayloadItems[] = new CtasPayload([
            'ctas' => $softwareInfoPayload->getActionButtons(),
        ]);

        return ($this->responder)(
            ($this->extractMetaFromPath)($contentPath),
            new ModulePayload(['items' => $modulePayloadItems]),
            $softwareInfoPayload,
            $uriPath
        );
    }
}
