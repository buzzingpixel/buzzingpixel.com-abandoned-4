<?php

declare(strict_types=1);

namespace App\Http\Home;

use App\Content\Meta\MetaPayload;
use App\Content\Modules\ModulePayload;
use App\HttpRespose\Minifier;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use Twig\Environment as TwigEnvironment;

class GetHomeResponder
{
    /** @var ResponseFactoryInterface */
    private $responseFactory;
    /** @var TwigEnvironment */
    private $twigEnvironment;
    /** @var Minifier */
    private $minifier;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        TwigEnvironment $twigEnvironment,
        Minifier $minifier
    ) {
        $this->responseFactory = $responseFactory;
        $this->twigEnvironment = $twigEnvironment;
        $this->minifier        = $minifier;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(
        MetaPayload $metaPayload,
        ModulePayload $modulePayload
    ) : ResponseInterface {
        $response = $this->responseFactory->createResponse();

        $response->getBody()->write(($this->minifier)(
            $this->twigEnvironment->render('StandardPage.twig', [
                'metaPayload' => $metaPayload,
                'modulePayload' => $modulePayload,
            ])
        ));

        return $response;
    }
}
