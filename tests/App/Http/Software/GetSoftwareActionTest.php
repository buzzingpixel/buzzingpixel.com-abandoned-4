<?php

declare(strict_types=1);

namespace Tests\App\Http\Software;

use App\Content\Meta\ExtractMetaFromPath;
use App\Content\Meta\MetaPayload;
use App\Content\Modules\ExtractModulesFromPath;
use App\Content\Modules\ModulePayload;
use App\Content\Software\ExtractSoftwareInfoFromPath;
use App\Content\Software\SoftwareInfoPayload;
use App\Http\Software\GetSoftwareAction;
use App\Http\Software\GetSoftwareResponder;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Throwable;

class GetSoftwareActionTest extends TestCase
{
    /**
     * @throws Throwable
     */
    private function internalRunTest(string $contentPath) : void
    {
        $pathMap = [
            '/software/ansel-craft' => 'Software/AnselCraft',
            '/software/ansel-ee' => 'Software/AnselEE',
        ];

        /**
         * Software info payload
         */

        $softwareInfoPayload = new SoftwareInfoPayload();

        $extractSoftwareInfoFromPath = $this->createMock(ExtractSoftwareInfoFromPath::class);

        $extractSoftwareInfoFromPath->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($pathMap[$contentPath]))
            ->willReturn($softwareInfoPayload);

        /**
         * Meta payload
         */

        $metaPayload = new MetaPayload();

        $extractMetaFromPath = $this->createMock(ExtractMetaFromPath::class);

        $extractMetaFromPath->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($pathMap[$contentPath]))
            ->willReturn($metaPayload);

        /**
         * Module payload
         */

        $modulePayload = new ModulePayload();

        $extractModulesFromPath = $this->createMock(ExtractModulesFromPath::class);

        $extractModulesFromPath->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($pathMap[$contentPath]))
            ->willReturn($modulePayload);

        /**
         * Responder
         */

        $responseInterface = $this->createMock(ResponseInterface::class);

        $responder = $this->createMock(GetSoftwareResponder::class);

        $responder->expects(self::once())
            ->method('__invoke')
            ->with(
                self::equalTo($metaPayload),
                self::isInstanceOf(ModulePayload::class),
                self::equalTo($softwareInfoPayload),
                self::equalTo($contentPath)
            )
            ->willReturn($responseInterface);

        /**
         * URI Interface
         */

        $uriInterface = $this->createMock(UriInterface::class);

        $uriInterface->expects(self::once())
            ->method('getPath')
            ->willReturn($contentPath);

        /**
         * Request interface
         */

        $requestInterface = $this->createMock(ServerRequestInterface::class);

        $requestInterface->method('getUri')->willReturn($uriInterface);

        /**
         * Test
         */

        $response = (new GetSoftwareAction(
            $responder,
            $extractMetaFromPath,
            $extractModulesFromPath,
            $extractSoftwareInfoFromPath
        ))($requestInterface);

        self::assertSame($responseInterface, $response);
    }

    /**
     * @throws Throwable
     */
    public function testAnselCraft() : void
    {
        $this->internalRunTest('/software/ansel-craft');
    }

    /**
     * @throws Throwable
     */
    public function testAnselEe() : void
    {
        $this->internalRunTest('/software/ansel-ee');
    }
}
