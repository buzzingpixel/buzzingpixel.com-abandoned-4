<?php

declare(strict_types=1);

namespace Tests\Software;

use App\Payload\Payload;
use App\Software\Models\SoftwareModel;
use App\Software\Models\SoftwareVersionModel;
use App\Software\Services\DeleteSoftware;
use App\Software\Services\DeleteSoftwareVersion;
use App\Software\Services\FetchAllSoftware;
use App\Software\Services\FetchSoftwareById;
use App\Software\Services\FetchSoftwareBySlug;
use App\Software\Services\FetchSoftwareVersionById;
use App\Software\Services\SaveSoftwareMaster;
use App\Software\SoftwareApi;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class SoftwareApiTest extends TestCase
{
    private SoftwareApi $api;

    private SoftwareModel $softwareModel;
    private SoftwareVersionModel $softwareVersionModel;
    private string $slug;
    private string $id;

    public function testSaveSoftware() : void
    {
        $payload = $this->api->saveSoftware($this->softwareModel);

        self::assertSame(
            'SaveSoftwarePayload',
            $payload->getResult()['message']
        );
    }

    public function testFetchSoftwareById() : void
    {
        $model = $this->api->fetchSoftwareById($this->id);

        self::assertSame($this->softwareModel, $model);
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

    public function testFetchSoftwareVersionById() : void
    {
        $model = $this->api->fetchSoftwareVersionById($this->id);

        self::assertSame($this->softwareVersionModel, $model);
    }

    public function testDeleteSoftware() : void
    {
        $this->api->deleteSoftware($this->softwareModel);
    }

    public function testDeleteSoftwareVersion() : void
    {
        $this->api->deleteSoftwareVersion($this->softwareVersionModel);
    }

    protected function setUp() : void
    {
        $this->softwareModel = new SoftwareModel();

        $this->softwareVersionModel = new SoftwareVersionModel();

        $this->slug = 'foo-slug';

        $this->id = 'foo-id';

        $this->api = new SoftwareApi($this->mockDi());
    }

    /**
     * @return ContainerInterface&MockObject
     */
    private function mockDi() : ContainerInterface
    {
        $di = $this->createMock(ContainerInterface::class);

        $di->method('get')->willReturnCallback(
            [$this, 'diGetCallback']
        );

        return $di;
    }

    /**
     * @return mixed
     */
    public function diGetCallback(string $class)
    {
        if ($class === SaveSoftwareMaster::class) {
            return $this->mockSaveSoftware();
        }

        if ($class === FetchSoftwareById::class) {
            return $this->mockFetchSoftwareById();
        }

        if ($class === FetchSoftwareBySlug::class) {
            return $this->mockFetchSoftwareBySlug();
        }

        if ($class === FetchAllSoftware::class) {
            return $this->mockFetchAllSoftware();
        }

        if ($class === FetchSoftwareVersionById::class) {
            return $this->mockFetchSoftwareVersionById();
        }

        if ($class === DeleteSoftware::class) {
            return $this->mockDeleteSoftware();
        }

        if ($class === DeleteSoftwareVersion::class) {
            return $this->mockDeleteSoftwareVersion();
        }

        return null;
    }

    /**
     * @return SaveSoftwareMaster&MockObject
     */
    private function mockSaveSoftware() : SaveSoftwareMaster
    {
        $payload = new Payload(
            Payload::STATUS_SUCCESSFUL,
            ['message' => 'SaveSoftwarePayload']
        );

        $mock = $this->createMock(SaveSoftwareMaster::class);

        $mock->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($this->softwareModel))
            ->willReturn($payload);

        return $mock;
    }

    /**
     * @return FetchSoftwareById&MockObject
     */
    private function mockFetchSoftwareById() : FetchSoftwareById
    {
        $mock = $this->createMock(FetchSoftwareById::class);

        $mock->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($this->id))
            ->willReturn($this->softwareModel);

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

    /**
     * @return FetchSoftwareVersionById&MockObject
     */
    private function mockFetchSoftwareVersionById() : FetchSoftwareVersionById
    {
        $mock = $this->createMock(FetchSoftwareVersionById::class);

        $mock->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($this->id))
            ->willReturn($this->softwareVersionModel);

        return $mock;
    }

    /**
     * @return DeleteSoftware&MockObject
     */
    private function mockDeleteSoftware() : DeleteSoftware
    {
        $mock = $this->createMock(DeleteSoftware::class);

        $mock->expects(self::once())
            ->method('__invoke');

        return $mock;
    }

    /**
     * @return DeleteSoftwareVersion&MockObject
     */
    private function mockDeleteSoftwareVersion() : DeleteSoftwareVersion
    {
        $mock = $this->createMock(DeleteSoftwareVersion::class);

        $mock->expects(self::once())
            ->method('__invoke');

        return $mock;
    }
}
