<?php

declare(strict_types=1);

namespace App\Http\Admin;

use App\Content\Meta\MetaPayload;
use App\Http\StandardResponderConstructor;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GetAdminResponder extends StandardResponderConstructor
{
    /**
     * @throws Throwable
     */
    public function __invoke(
        string $template,
        string $metaTitle,
        string $activeTab
    ) : ResponseInterface {
        $response = $this->responseFactory->createResponse();

        $response->getBody()->write(
            ($this->minifier)(
                $this->twigEnvironment->render(
                    $template,
                    [
                        'metaPayload' => new MetaPayload(
                            ['metaTitle' => $metaTitle]
                        ),
                        'activeTab' => $activeTab,
                    ]
                )
            )
        );

        return $response;
    }
}
