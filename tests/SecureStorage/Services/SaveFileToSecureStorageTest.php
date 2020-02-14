<?php

declare(strict_types=1);

namespace Tests\SecureStorage\Services;

use App\Payload\Payload;
use App\SecureStorage\Services\SaveFileToSecureStorage;
use Config\General;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Component\Filesystem\Filesystem;

class SaveFileToSecureStorageTest extends TestCase
{
    public function testWhenGeneralConfigThrowsException() : void
    {
        $generalConfig = $this->createMock(General::class);

        $generalConfig->method(self::anything())
            ->willThrowException(new Exception());

        $filesystem = $this->createMock(Filesystem::class);

        $service = new SaveFileToSecureStorage(
            $generalConfig,
            $filesystem
        );

        $uploadedFile = $this->createMock(
            UploadedFileInterface::class
        );

        $payload = $service($uploadedFile);

        self::assertSame(
            Payload::STATUS_ERROR,
            $payload->getStatus()
        );
    }

    public function testWhenFileSystemThrowsException() : void
    {
        $generalConfig = $this->createMock(General::class);

        $filesystem = $this->createMock(Filesystem::class);

        $filesystem->method(self::anything())
            ->willThrowException(new Exception());

        $service = new SaveFileToSecureStorage(
            $generalConfig,
            $filesystem
        );

        $uploadedFile = $this->createMock(
            UploadedFileInterface::class
        );

        $payload = $service($uploadedFile);

        self::assertSame(
            Payload::STATUS_ERROR,
            $payload->getStatus()
        );
    }

    public function testWhenNoDirectory() : void
    {
        $generalConfig = $this->createMock(General::class);

        $generalConfig->method('__call')->willReturnCallback(
            static function (string $method) {
                if ($method === 'pathToSecureStorageDirectory') {
                    return '/foo/path';
                }

                throw new Exception('Invalid method call');
            }
        );

        $filesystem = $this->createMock(Filesystem::class);

        $filesystem->expects(self::once())
            ->method('mkdir')
            ->with(self::equalTo('/foo/path'));

        $service = new SaveFileToSecureStorage(
            $generalConfig,
            $filesystem
        );

        $uploadedFile = $this->createMock(
            UploadedFileInterface::class
        );

        $uploadedFile->expects(self::once())
            ->method('getClientFilename')
            ->willReturn('test-name.ext');

        $uploadedFile->expects(self::once())
            ->method('moveTo')
            ->with(self::equalTo('/foo/path/test-name.ext'))
            ->willReturn(true);

        $payload = $service($uploadedFile);

        self::assertSame(
            Payload::STATUS_SUCCESSFUL,
            $payload->getStatus()
        );
    }

    public function testWithDirectory() : void
    {
        $generalConfig = $this->createMock(General::class);

        $generalConfig->method('__call')->willReturnCallback(
            static function (string $method) {
                if ($method === 'pathToSecureStorageDirectory') {
                    return '/foo/path';
                }

                throw new Exception('Invalid method call');
            }
        );

        $filesystem = $this->createMock(Filesystem::class);

        $filesystem->expects(self::once())
            ->method('mkdir')
            ->with(self::equalTo('/foo/path/bar'));

        $service = new SaveFileToSecureStorage(
            $generalConfig,
            $filesystem
        );

        $uploadedFile = $this->createMock(
            UploadedFileInterface::class
        );

        $uploadedFile->expects(self::once())
            ->method('getClientFilename')
            ->willReturn('test-name.ext');

        $uploadedFile->expects(self::once())
            ->method('moveTo')
            ->with(self::equalTo('/foo/path/bar/test-name.ext'))
            ->willReturn(true);

        $payload = $service($uploadedFile, 'bar');

        self::assertSame(
            Payload::STATUS_SUCCESSFUL,
            $payload->getStatus()
        );
    }
}
