<?php

declare(strict_types=1);

namespace App\Http\Tinker;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use function dd;
use function getenv;

class GetTinkerAction
{
    private ContainerInterface $di;

    public function __construct(ContainerInterface $di)
    {
        $this->di = $di;
    }

    /**
     * @throws HttpNotFoundException
     */
    public function __invoke(ServerRequestInterface $request) : void
    {
        if (getenv('DEV_MODE') !== 'true') {
            throw new HttpNotFoundException($request);
        }

        dd('Tinker', $this->di);
    }
}
