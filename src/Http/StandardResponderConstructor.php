<?php

declare(strict_types=1);

namespace App\Http;

use App\HttpResponse\Minifier;
use Psr\Http\Message\ResponseFactoryInterface;
use Twig\Environment as TwigEnvironment;

abstract class StandardResponderConstructor
{
    /** @var ResponseFactoryInterface */
    protected $responseFactory;
    /** @var TwigEnvironment */
    protected $twigEnvironment;
    /** @var Minifier */
    protected $minifier;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        TwigEnvironment $twigEnvironment,
        Minifier $minifier
    ) {
        $this->responseFactory = $responseFactory;
        $this->twigEnvironment = $twigEnvironment;
        $this->minifier        = $minifier;
    }
}
