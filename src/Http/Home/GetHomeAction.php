<?php

declare(strict_types=1);

namespace App\Http\Home;

use App\Content\Meta\ExtractMetaFromPath;
use App\Content\Modules\ExtractModulesFromPath;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GetHomeAction
{
    private GetHomeResponder $responder;
    private ExtractMetaFromPath $extractMetaFromPath;
    private ExtractModulesFromPath $extractModulesFromPath;

    public function __construct(
        GetHomeResponder $responder,
        ExtractMetaFromPath $extractMetaFromPath,
        ExtractModulesFromPath $extractModulesFromPath
    ) {
        $this->responder              = $responder;
        $this->extractMetaFromPath    = $extractMetaFromPath;
        $this->extractModulesFromPath = $extractModulesFromPath;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(): ResponseInterface
    {
        return ($this->responder)(
            ($this->extractMetaFromPath)('HomePage'),
            ($this->extractModulesFromPath)('HomePage')
        );
    }
}
