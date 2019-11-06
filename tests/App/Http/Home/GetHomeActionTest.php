<?php

declare(strict_types=1);

namespace Tests\App\Http\Home;

use App\Content\Meta\ExtractMetaFromPath;
use App\Content\Meta\MetaPayload;
use App\Content\Modules\ExtractModulesFromPath;
use App\Content\Modules\ModulePayload;
use App\Http\Home\GetHomeAction;
use App\Http\Home\GetHomeResponder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GetHomeActionTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
        /**
         * Meta payload
         */

        $metaPayload = new MetaPayload();

        /** @var ExtractMetaFromPath&MockObject $extractMetaFromPath */
        $extractMetaFromPath = $this->createMock(ExtractMetaFromPath::class);

        $extractMetaFromPath->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo('HomePage'))
            ->willReturn($metaPayload);

        /**
         * Module payload
         */

        $modulePayload = new ModulePayload();

        /** @var ExtractModulesFromPath&MockObject $extractModulesFromPath */
        $extractModulesFromPath = $this->createMock(ExtractModulesFromPath::class);

        $extractModulesFromPath->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo('HomePage'))
            ->willReturn($modulePayload);

        /**
         * Responder
         */

        $responseInterface = $this->createMock(ResponseInterface::class);

        /** @var GetHomeResponder&MockObject $responder */
        $responder = $this->createMock(GetHomeResponder::class);

        $responder->expects(self::once())
            ->method('__invoke')
            ->with(
                self::equalTo($metaPayload),
                self::equalTo($modulePayload)
            )
            ->willReturn($responseInterface);

        /**
         * Test
         */

        $response = (new GetHomeAction(
            $responder,
            $extractMetaFromPath,
            $extractModulesFromPath
        ))();

        self::assertSame($responseInterface, $response);
    }
}
