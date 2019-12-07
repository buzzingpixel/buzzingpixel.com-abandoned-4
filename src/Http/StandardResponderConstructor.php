<?php

declare(strict_types=1);

namespace App\Http;

use Psr\Http\Message\ResponseFactoryInterface;
use Twig\Environment as TwigEnvironment;

abstract class StandardResponderConstructor
{
    /** @var ResponseFactoryInterface */
    protected $responseFactory;
    /** @var TwigEnvironment */
    protected $twigEnvironment;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        TwigEnvironment $twigEnvironment
    ) {
        $this->responseFactory = $responseFactory;
        $this->twigEnvironment = $twigEnvironment;
    }
}
