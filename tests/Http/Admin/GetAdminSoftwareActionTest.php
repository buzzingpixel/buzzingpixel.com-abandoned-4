<?php

declare(strict_types=1);

namespace Tests\Http\Admin;

use App\Http\Admin\GetAdminResponder;
use App\Http\Admin\GetAdminSoftwareAction;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GetAdminSoftwareActionTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $response = $this->createMock(
            ResponseInterface::class
        );

        $responder = $this->createMock(GetAdminResponder::class);

        $responder->expects(self::once())
            ->method('__invoke')
            ->with(
                self::equalTo('Admin/Software.twig'),
                self::equalTo('Software | Admin'),
                self::equalTo('software')
            )
            ->willReturn($response);

        $action = new GetAdminSoftwareAction($responder);

        $returnResponse = $action();

        self::assertSame($response, $returnResponse);
    }
}
