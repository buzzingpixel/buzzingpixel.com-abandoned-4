<?php

declare(strict_types=1);

namespace Tests\Software\Services;

use App\Payload\Payload;
use App\Persistence\SaveExistingRecord;
use App\Persistence\SaveNewRecord;
use App\Persistence\Software\SoftwareRecord;
use App\Persistence\Software\SoftwareVersionRecord;
use App\Persistence\UuidFactoryWithOrderedTimeCodec;
use App\SecureStorage\Services\SaveFileToSecureStorage;
use App\Software\Models\SoftwareModel;
use App\Software\Models\SoftwareVersionModel;
use App\Software\Services\SaveSoftware;
use App\Software\Transformers\TransformSoftwareModelToRecord;
use App\Software\Transformers\TransformSoftwareVersionModelToRecord;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use PDO;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;
use stdClass;
use Tests\TestConfig;

class SaveSoftwareTest extends TestCase
{
    public function testWhenBeginTransactionThrowsException() : void
    {
        $pdo = $this->createMock(PDO::class);

        $pdo->expects(self::at(0))
            ->method('beginTransaction')
            ->willThrowException(new Exception());

        $pdo->expects(self::at(1))
            ->method('rollBack')
            ->willReturn(true);

        $service = new SaveSoftware(
            $pdo,
            TestConfig::$di->get(
                UuidFactoryWithOrderedTimeCodec::class
            ),
            $this->createMock(
                SaveNewRecord::class
            ),
            $this->createMock(
                SaveExistingRecord::class
            ),
            TestConfig::$di->get(
                TransformSoftwareModelToRecord::class
            ),
            TestConfig::$di->get(
                TransformSoftwareVersionModelToRecord::class
            ),
            TestConfig::$di->get(
                SaveFileToSecureStorage::class
            )
        );

        $payload = $service(new SoftwareModel());

        self::assertSame(
            Payload::STATUS_ERROR,
            $payload->getStatus()
        );

        self::assertSame([], $payload->getResult());
    }

    public function testWhenSaveNewSoftwareFailsNoVersions() : void
    {
        $pdo = $this->createMock(PDO::class);

        $pdo->expects(self::at(0))
            ->method('beginTransaction')
            ->willReturn(true);

        $pdo->expects(self::at(1))
            ->method('rollBack')
            ->willReturn(true);

        $recordHolder       = new stdClass();
        $recordHolder->call = null;

        $saveNewRecord = $this->createMock(
            SaveNewRecord::class
        );

        $saveNewRecord->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (SoftwareRecord $record) use (
                    $recordHolder
                ) {
                    $recordHolder->call = $record;

                    return new Payload(Payload::STATUS_ERROR);
                }
            );

        $service = new SaveSoftware(
            $pdo,
            TestConfig::$di->get(
                UuidFactoryWithOrderedTimeCodec::class
            ),
            $saveNewRecord,
            $this->createMock(
                SaveExistingRecord::class
            ),
            TestConfig::$di->get(
                TransformSoftwareModelToRecord::class
            ),
            TestConfig::$di->get(
                TransformSoftwareVersionModelToRecord::class
            ),
            TestConfig::$di->get(
                SaveFileToSecureStorage::class
            )
        );

        $payload = $service(new SoftwareModel([
            'slug' => 'foo-slug',
            'name' => 'Foo Name',
            'isForSale' => false,
        ]));

        self::assertSame(
            Payload::STATUS_ERROR,
            $payload->getStatus()
        );

        self::assertSame([], $payload->getResult());

        /** @var SoftwareRecord|null $record */
        $record = $recordHolder->call;

        self::assertInstanceOf(
            SoftwareRecord::class,
            $record
        );

        self::assertSame('foo-slug', $record->slug);

        self::assertSame('Foo Name', $record->name);

        self::assertSame('0', $record->is_for_sale);

        self::assertNotEmpty($record->id);
    }

    public function testSaveNewSoftwareNoVersions() : void
    {
        $pdo = $this->createMock(PDO::class);

        $pdo->expects(self::at(0))
            ->method('beginTransaction')
            ->willReturn(true);

        $pdo->expects(self::at(1))
            ->method('commit')
            ->willReturn(true);

        $recordHolder       = new stdClass();
        $recordHolder->call = null;

        $saveNewRecord = $this->createMock(
            SaveNewRecord::class
        );

        $saveNewRecord->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (SoftwareRecord $record) use (
                    $recordHolder
                ) {
                    $recordHolder->call = $record;

                    return new Payload(Payload::STATUS_CREATED);
                }
            );

        $service = new SaveSoftware(
            $pdo,
            TestConfig::$di->get(
                UuidFactoryWithOrderedTimeCodec::class
            ),
            $saveNewRecord,
            $this->createMock(
                SaveExistingRecord::class
            ),
            TestConfig::$di->get(
                TransformSoftwareModelToRecord::class
            ),
            TestConfig::$di->get(
                TransformSoftwareVersionModelToRecord::class
            ),
            TestConfig::$di->get(
                SaveFileToSecureStorage::class
            )
        );

        $payload = $service(new SoftwareModel([
            'slug' => 'foo-slug',
            'name' => 'Foo Name',
            'isForSale' => false,
        ]));

        self::assertSame(
            Payload::STATUS_CREATED,
            $payload->getStatus()
        );

        self::assertSame([], $payload->getResult());

        /** @var SoftwareRecord|null $record */
        $record = $recordHolder->call;

        self::assertInstanceOf(
            SoftwareRecord::class,
            $record
        );

        self::assertSame('foo-slug', $record->slug);

        self::assertSame('Foo Name', $record->name);

        self::assertSame('0', $record->is_for_sale);

        self::assertNotEmpty($record->id);
    }

    public function testWhenSaveExistingSoftwareFailsNoVersions() : void
    {
        $pdo = $this->createMock(PDO::class);

        $pdo->expects(self::at(0))
            ->method('beginTransaction')
            ->willReturn(true);

        $pdo->expects(self::at(1))
            ->method('rollBack')
            ->willReturn(true);

        $recordHolder       = new stdClass();
        $recordHolder->call = null;

        $saveExistingRecord = $this->createMock(
            SaveExistingRecord::class
        );

        $saveExistingRecord->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (SoftwareRecord $record) use (
                    $recordHolder
                ) {
                    $recordHolder->call = $record;

                    return new Payload(Payload::STATUS_ERROR);
                }
            );

        $service = new SaveSoftware(
            $pdo,
            TestConfig::$di->get(
                UuidFactoryWithOrderedTimeCodec::class
            ),
            $this->createMock(
                SaveNewRecord::class
            ),
            $saveExistingRecord,
            TestConfig::$di->get(
                TransformSoftwareModelToRecord::class
            ),
            TestConfig::$di->get(
                TransformSoftwareVersionModelToRecord::class
            ),
            TestConfig::$di->get(
                SaveFileToSecureStorage::class
            )
        );

        $payload = $service(new SoftwareModel([
            'id' => 'foo-id',
            'slug' => 'foo-slug',
            'name' => 'Foo Name',
            'isForSale' => false,
        ]));

        self::assertSame(
            Payload::STATUS_ERROR,
            $payload->getStatus()
        );

        self::assertSame([], $payload->getResult());

        /** @var SoftwareRecord|null $record */
        $record = $recordHolder->call;

        self::assertInstanceOf(
            SoftwareRecord::class,
            $record
        );

        self::assertSame('foo-slug', $record->slug);

        self::assertSame('Foo Name', $record->name);

        self::assertSame('0', $record->is_for_sale);

        self::assertSame('foo-id', $record->id);
    }

    public function testSaveExistingSoftwareNoVersions() : void
    {
        $pdo = $this->createMock(PDO::class);

        $pdo->expects(self::at(0))
            ->method('beginTransaction')
            ->willReturn(true);

        $pdo->expects(self::at(1))
            ->method('commit')
            ->willReturn(true);

        $recordHolder       = new stdClass();
        $recordHolder->call = null;

        $saveExistingRecord = $this->createMock(
            SaveExistingRecord::class
        );

        $saveExistingRecord->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (SoftwareRecord $record) use (
                    $recordHolder
                ) {
                    $recordHolder->call = $record;

                    return new Payload(Payload::STATUS_UPDATED);
                }
            );

        $service = new SaveSoftware(
            $pdo,
            TestConfig::$di->get(
                UuidFactoryWithOrderedTimeCodec::class
            ),
            $this->createMock(
                SaveNewRecord::class
            ),
            $saveExistingRecord,
            TestConfig::$di->get(
                TransformSoftwareModelToRecord::class
            ),
            TestConfig::$di->get(
                TransformSoftwareVersionModelToRecord::class
            ),
            TestConfig::$di->get(
                SaveFileToSecureStorage::class
            )
        );

        $payload = $service(new SoftwareModel([
            'id' => 'foo-id',
            'slug' => 'foo-slug',
            'name' => 'Foo Name',
            'isForSale' => true,
        ]));

        self::assertSame(
            Payload::STATUS_UPDATED,
            $payload->getStatus()
        );

        self::assertSame([], $payload->getResult());

        /** @var SoftwareRecord|null $record */
        $record = $recordHolder->call;

        self::assertInstanceOf(
            SoftwareRecord::class,
            $record
        );

        self::assertSame('foo-slug', $record->slug);

        self::assertSame('Foo Name', $record->name);

        self::assertSame('1', $record->is_for_sale);

        self::assertSame('foo-id', $record->id);
    }

    public function testWhenSaveNewVersionFails() : void
    {
        $pdo = $this->createMock(PDO::class);

        $pdo->expects(self::at(0))
            ->method('beginTransaction')
            ->willReturn(true);

        $pdo->expects(self::at(1))
            ->method('rollBack')
            ->willReturn(true);

        $saveCallHolder       = new stdClass();
        $saveCallHolder->call = null;

        $saveNewRecord = $this->createMock(
            SaveNewRecord::class
        );

        $saveNewRecord->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (SoftwareVersionRecord $record) use (
                    $saveCallHolder
                ) {
                    $saveCallHolder->call = $record;

                    return new Payload(Payload::STATUS_ERROR);
                }
            );

        $service = new SaveSoftware(
            $pdo,
            TestConfig::$di->get(
                UuidFactoryWithOrderedTimeCodec::class
            ),
            $saveNewRecord,
            $this->createMock(
                SaveExistingRecord::class
            ),
            TestConfig::$di->get(
                TransformSoftwareModelToRecord::class
            ),
            TestConfig::$di->get(
                TransformSoftwareVersionModelToRecord::class
            ),
            TestConfig::$di->get(
                SaveFileToSecureStorage::class
            )
        );

        $payload = $service(new SoftwareModel([
            'id' => 'foo-id',
            'slug' => 'foo-slug',
            'name' => 'Foo Name',
            'isForSale' => true,
            'versions' => [
                new SoftwareVersionModel([
                    'majorVersion' => '4',
                    'version' => '4.0.0',
                    'downloadFile' => '/foo/bar/baz/download',
                ]),
            ],
        ]));

        self::assertSame(
            Payload::STATUS_ERROR,
            $payload->getStatus()
        );

        self::assertSame([], $payload->getResult());

        /** @var SoftwareVersionRecord|null $versionRecord */
        $versionRecord = $saveCallHolder->call;

        self::assertInstanceOf(
            SoftwareVersionRecord::class,
            $versionRecord
        );

        self::assertSame('foo-id', $versionRecord->software_id);

        self::assertSame('4', $versionRecord->major_version);

        self::assertSame('4.0.0', $versionRecord->version);

        self::assertSame(
            '/foo/bar/baz/download',
            $versionRecord->download_file
        );

        self::assertNotEmpty($versionRecord->released_on);

        self::assertNotEmpty($versionRecord->id);
    }

    public function testSaveNewVersion() : void
    {
        $pdo = $this->createMock(PDO::class);

        $pdo->expects(self::at(0))
            ->method('beginTransaction')
            ->willReturn(true);

        $pdo->expects(self::at(1))
            ->method('rollBack')
            ->willReturn(true);

        $saveCallHolder       = new stdClass();
        $saveCallHolder->call = [];

        $saveNewRecord = $this->createMock(
            SaveNewRecord::class
        );

        $saveNewRecord->method('__invoke')
            ->willReturnCallback(
                static function (SoftwareVersionRecord $record) use (
                    $saveCallHolder
                ) {
                    $saveCallHolder->call[] = $record;

                    return new Payload(Payload::STATUS_CREATED);
                }
            );

        $saveExistingRecord = $this->createMock(
            SaveExistingRecord::class
        );

        $saveExistingRecord->method('__invoke')
            ->willReturn(new Payload(Payload::STATUS_UPDATED));

        $service = new SaveSoftware(
            $pdo,
            TestConfig::$di->get(
                UuidFactoryWithOrderedTimeCodec::class
            ),
            $saveNewRecord,
            $saveExistingRecord,
            TestConfig::$di->get(
                TransformSoftwareModelToRecord::class
            ),
            TestConfig::$di->get(
                TransformSoftwareVersionModelToRecord::class
            ),
            TestConfig::$di->get(
                SaveFileToSecureStorage::class
            )
        );

        $payload = $service(new SoftwareModel([
            'id' => 'foo-id',
            'slug' => 'foo-slug',
            'name' => 'Foo Name',
            'isForSale' => true,
            'versions' => [
                new SoftwareVersionModel([
                    'majorVersion' => '4',
                    'version' => '4.0.0',
                    'downloadFile' => '/bar/baz/download',
                ]),
                new SoftwareVersionModel([
                    'majorVersion' => '6',
                    'version' => '6.10.455',
                    'downloadFile' => '/baz/download',
                ]),
            ],
        ]));

        self::assertSame(
            Payload::STATUS_UPDATED,
            $payload->getStatus()
        );

        self::assertSame([], $payload->getResult());

        self::assertCount(2, $saveCallHolder->call);

        /** @var SoftwareVersionRecord|null $versionRecord */
        $versionRecord1 = $saveCallHolder->call[0];

        self::assertInstanceOf(
            SoftwareVersionRecord::class,
            $versionRecord1
        );

        self::assertSame('foo-id', $versionRecord1->software_id);

        self::assertSame('4', $versionRecord1->major_version);

        self::assertSame('4.0.0', $versionRecord1->version);

        self::assertSame(
            '/bar/baz/download',
            $versionRecord1->download_file
        );

        self::assertNotEmpty($versionRecord1->released_on);

        self::assertNotEmpty($versionRecord1->id);

        /** @var SoftwareVersionRecord|null $versionRecord */
        $versionRecord2 = $saveCallHolder->call[1];

        self::assertInstanceOf(
            SoftwareVersionRecord::class,
            $versionRecord2
        );

        self::assertSame('foo-id', $versionRecord2->software_id);

        self::assertSame('6', $versionRecord2->major_version);

        self::assertSame('6.10.455', $versionRecord2->version);

        self::assertSame(
            '/baz/download',
            $versionRecord2->download_file
        );

        self::assertNotEmpty($versionRecord2->released_on);

        self::assertNotEmpty($versionRecord2->id);
    }

    public function testWhenSaveExistingVersionFails() : void
    {
        $pdo = $this->createMock(PDO::class);

        $pdo->expects(self::at(0))
            ->method('beginTransaction')
            ->willReturn(true);

        $pdo->expects(self::at(1))
            ->method('rollBack')
            ->willReturn(true);

        $saveCallHolder       = new stdClass();
        $saveCallHolder->call = null;

        $saveExistingRecord = $this->createMock(
            SaveExistingRecord::class
        );

        $saveExistingRecord->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (SoftwareVersionRecord $record) use (
                    $saveCallHolder
                ) {
                    $saveCallHolder->call = $record;

                    return new Payload(Payload::STATUS_ERROR);
                }
            );

        $service = new SaveSoftware(
            $pdo,
            TestConfig::$di->get(
                UuidFactoryWithOrderedTimeCodec::class
            ),
            $this->createMock(
                SaveNewRecord::class
            ),
            $saveExistingRecord,
            TestConfig::$di->get(
                TransformSoftwareModelToRecord::class
            ),
            TestConfig::$di->get(
                TransformSoftwareVersionModelToRecord::class
            ),
            TestConfig::$di->get(
                SaveFileToSecureStorage::class
            )
        );

        $payload = $service(new SoftwareModel([
            'id' => 'foo-id',
            'slug' => 'foo-slug',
            'name' => 'Foo Name',
            'isForSale' => true,
            'versions' => [
                new SoftwareVersionModel([
                    'id' => 'vid1',
                    'majorVersion' => '4',
                    'version' => '4.0.0',
                    'downloadFile' => '/foo/bar/baz/download',
                ]),
            ],
        ]));

        self::assertSame(
            Payload::STATUS_ERROR,
            $payload->getStatus()
        );

        self::assertSame([], $payload->getResult());

        /** @var SoftwareVersionRecord|null $versionRecord */
        $versionRecord = $saveCallHolder->call;

        self::assertInstanceOf(
            SoftwareVersionRecord::class,
            $versionRecord
        );

        self::assertSame('foo-id', $versionRecord->software_id);

        self::assertSame('4', $versionRecord->major_version);

        self::assertSame('4.0.0', $versionRecord->version);

        self::assertSame(
            '/foo/bar/baz/download',
            $versionRecord->download_file
        );

        self::assertNotEmpty($versionRecord->released_on);

        self::assertNotEmpty($versionRecord->id);
    }

    public function testSaveExistingVersion() : void
    {
        $pdo = $this->createMock(PDO::class);

        $pdo->expects(self::at(0))
            ->method('beginTransaction')
            ->willReturn(true);

        $pdo->expects(self::at(1))
            ->method('commit')
            ->willReturn(true);

        $saveCallHolder       = new stdClass();
        $saveCallHolder->call = [];

        $saveNewRecord = $this->createMock(
            SaveNewRecord::class
        );

        $saveNewRecord->method('__invoke')
            ->willReturn(new Payload(Payload::STATUS_CREATED));

        $saveExistingRecord = $this->createMock(
            SaveExistingRecord::class
        );

        $saveExistingRecord->method('__invoke')
            ->willReturnCallback(
                static function (SoftwareVersionRecord $record) use (
                    $saveCallHolder
                ) {
                    $saveCallHolder->call[] = $record;

                    return new Payload(Payload::STATUS_UPDATED);
                }
            );

        $newDownloadFile = $this->createMock(
            UploadedFileInterface::class
        );

        $newDownloadFile->method('getClientFilename')
            ->willReturn('foo-client-filename');

        $saveFileToSecureStorage = $this->createMock(
            SaveFileToSecureStorage::class
        );

        $saveFileToSecureStorage->expects(self::once())
            ->method('__invoke')
            ->with(
                self::equalTo($newDownloadFile),
                self::equalTo('foo-slug')
            )
            ->willReturn(new Payload(Payload::STATUS_SUCCESSFUL));

        $service = new SaveSoftware(
            $pdo,
            TestConfig::$di->get(
                UuidFactoryWithOrderedTimeCodec::class
            ),
            $saveNewRecord,
            $saveExistingRecord,
            TestConfig::$di->get(
                TransformSoftwareModelToRecord::class
            ),
            TestConfig::$di->get(
                TransformSoftwareVersionModelToRecord::class
            ),
            $saveFileToSecureStorage,
        );

        $releasedOn1 = new DateTimeImmutable('100 days ago');

        $releasedOn2 = new DateTimeImmutable('now');

        $payload = $service(new SoftwareModel([
            'slug' => 'foo-slug',
            'name' => 'Foo Name',
            'isForSale' => true,
            'versions' => [
                new SoftwareVersionModel([
                    'id' => 'vid1',
                    'majorVersion' => '4',
                    'version' => '4.0.0',
                    'downloadFile' => '/bar/baz/download',
                    'newDownloadFile' => $newDownloadFile,
                    'releasedOn' => $releasedOn1,
                ]),
                new SoftwareVersionModel([
                    'id' => 'vid2',
                    'majorVersion' => '6',
                    'version' => '6.10.455',
                    'downloadFile' => '/baz/download',
                    'releasedOn' => $releasedOn2,
                ]),
            ],
        ]));

        self::assertSame(
            Payload::STATUS_CREATED,
            $payload->getStatus()
        );

        self::assertSame([], $payload->getResult());

        self::assertCount(2, $saveCallHolder->call);

        /** @var SoftwareVersionRecord|null $versionRecord */
        $versionRecord1 = $saveCallHolder->call[0];

        self::assertInstanceOf(
            SoftwareVersionRecord::class,
            $versionRecord1
        );

        self::assertNotEmpty($versionRecord1->software_id);

        self::assertSame('4', $versionRecord1->major_version);

        self::assertSame('4.0.0', $versionRecord1->version);

        self::assertSame(
            'foo-slug/foo-client-filename',
            $versionRecord1->download_file
        );

        self::assertSame(
            $releasedOn1->format(DateTimeInterface::ATOM),
            $versionRecord1->released_on
        );

        self::assertNotEmpty($versionRecord1->released_on);

        self::assertSame('vid1', $versionRecord1->id);

        /** @var SoftwareVersionRecord|null $versionRecord */
        $versionRecord2 = $saveCallHolder->call[1];

        self::assertInstanceOf(
            SoftwareVersionRecord::class,
            $versionRecord2
        );

        self::assertNotEmpty($versionRecord2->software_id);

        self::assertSame('6', $versionRecord2->major_version);

        self::assertSame('6.10.455', $versionRecord2->version);

        self::assertSame(
            '/baz/download',
            $versionRecord2->download_file
        );

        self::assertSame(
            $releasedOn2->format(DateTimeInterface::ATOM),
            $versionRecord2->released_on
        );

        self::assertSame('vid2', $versionRecord2->id);
    }
}
