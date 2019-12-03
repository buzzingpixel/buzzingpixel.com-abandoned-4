<?php

declare(strict_types=1);

namespace App\Http\Admin;

use Psr\Http\Message\ResponseInterface;
use Throwable;

class GetAdminSoftwareAction
{
    /** @var GetAdminResponder */
    private $responder;

    public function __construct(GetAdminResponder $responder)
    {
        $this->responder = $responder;
    }

    /**
     * @throws Throwable
     */
    public function __invoke() : ResponseInterface
    {
        return ($this->responder)(
            'Admin/Software.twig',
            'Admin',
            'software'
        );
    }
}
