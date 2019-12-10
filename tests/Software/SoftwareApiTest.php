<?php

declare(strict_types=1);

namespace Tests\Software;

use App\Payload\Payload;
use App\Software\Models\SoftwareModel;
use App\Software\Services\FetchAllSoftware;
use App\Software\Services\FetchSoftwareBySlug;
use App\Software\Services\SaveSoftware;
use App\Software\SoftwareApi;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class SoftwareApiTest extends TestCase
{
    /** @var SoftwareApi */
    private $api;

    /** @var SoftwareModel */
    private $softwareModel;
    /** @var string */
    private $slug;

    public function testSaveSoftware() : void
    {
        $payload = $this->api->saveSoftware($this->softwareModel);

        self::assertSame(
            'SaveSoftwarePayload',
            $payload->getResult()['message']
        );
    }

    public function testFetchSoftwareBySlug() : void
    {
        $model = $this->api->fetchSoftwareBySlug($this->slug);

        self::assertSame($this->softwareModel, $model);
    }

    public function testFetchAllSoftware() : void
    {
        $models = $this->api->fetchAllSoftware();

        self::assertCount(1, $models);

        self::assertSame(
            $this->softwareModel,
            $models[0],
        );
    }

    protected function setUp() : void
    {
        $this->softwareModel = new SoftwareModel();

        $this->slug = 'foo-slug';

        $this->api = new SoftwareApi($this->mockDi());
    }

    /**
     * @return ContainerInterface&MockObject
     */
    private function mockDi() : ContainerInterface
    {
        $di = $this->createMock(ContainerInterface::class);

        $di->method('get')
            ->willReturnCallback([$this, 'diGetCallback']);

        return $di;
    }

    /**
     * @return mixed
     */
    public function diGetCallback(string $class)
    {
        if ($class === SaveSoftware::class) {
            return $this->mockSaveSoftware();
        }

        if ($class === FetchSoftwareBySlug::class) {
            return $this->mockFetchSoftwareBySlug();
        }

        if ($class === FetchAllSoftware::class) {
            return $this->mockFetchAllSoftware();
        }

        return null;
    }

    /**
     * @return SaveSoftware&MockObject
     */
    private function mockSaveSoftware() : SaveSoftware
    {
        $payload = new Payload(
            Payload::STATUS_SUCCESSFUL,
            ['message' => 'SaveSoftwarePayload']
        );

        $mock = $this->createMock(SaveSoftware::class);

        $mock->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($this->softwareModel))
            ->willReturn($payload);

        return $mock;
    }

    /**
     * @return FetchSoftwareBySlug&MockObject
     */
    private function mockFetchSoftwareBySlug() : FetchSoftwareBySlug
    {
        $mock = $this->createMock(FetchSoftwareBySlug::class);

        $mock->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($this->slug))
            ->willReturn($this->softwareModel);

        return $mock;
    }

    /**
     * @return FetchAllSoftware&MockObject
     */
    private function mockFetchAllSoftware() : FetchAllSoftware
    {
        $mock = $this->createMock(FetchAllSoftware::class);

        $mock->expects(self::once())
            ->method('__invoke')
            ->willReturn([$this->softwareModel]);

        return $mock;
    }
}
