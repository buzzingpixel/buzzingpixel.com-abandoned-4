<?php

declare(strict_types=1);

namespace Tests\Users\Services;

use App\Payload\Payload;
use App\Persistence\SaveNewRecord;
use App\Persistence\Users\UserSessionRecord;
use App\Persistence\UuidFactoryWithOrderedTimeCodec;
use App\Users\Models\UserModel;
use App\Users\Services\CreateUserSession;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\TestConfig;
use function func_get_args;

class CreateUserSessionTest extends TestCase
{
    public function testWhenNoId() : void
    {
        $service = new CreateUserSession(
            TestConfig::$di->get(UuidFactoryWithOrderedTimeCodec::class),
            $this->createMock(SaveNewRecord::class)
        );

        $payload = $service(new UserModel());

        self::assertSame(
            Payload::STATUS_NOT_CREATED,
            $payload->getStatus()
        );

        self::assertSame(
            ['message' => 'User ID is required'],
            $payload->getResult()
        );
    }

    /**
     * @throws Exception
     */
    public function test() : void
    {
        $this->saveNewRecordCallArgs = [];

        $uuidFactory = TestConfig::$di->get(UuidFactoryWithOrderedTimeCodec::class);

        $service = new CreateUserSession(
            $uuidFactory,
            $this->mockSaveNewRecord()
        );

        $userId = $uuidFactory->uuid1()->toString();

        $payload = $service(new UserModel(['id' => $userId]));

        self::assertSame(
            Payload::STATUS_CREATED,
            $payload->getStatus()
        );

        self::assertSame(
            ['message' => 'Foo Bar'],
            $payload->getResult()
        );

        /** @var array<int, mixed> $saveNewRecordCallArgs */
        $saveNewRecordCallArgs = $this->saveNewRecordCallArgs;

        self::assertCount(1, $saveNewRecordCallArgs);

        /** @var UserSessionRecord|null $userSessionRecord */
        $userSessionRecord = $saveNewRecordCallArgs[0];

        self::assertInstanceOf(UserSessionRecord::class, $userSessionRecord);

        self::assertSame(
            $userId,
            $userSessionRecord->user_id
        );

        self::assertNotEmpty($userSessionRecord->created_at);

        self::assertNotEmpty($userSessionRecord->last_touched_at);

        self::assertSame(
            $userSessionRecord->created_at,
            $userSessionRecord->last_touched_at
        );

        self::assertNotEmpty($userSessionRecord->id);
    }

    /**
     * @return SaveNewRecord&MockObject
     */
    private function mockSaveNewRecord() : SaveNewRecord
    {
        $mock = $this->createMock(SaveNewRecord::class);

        $mock->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback([$this, 'saveNewRecordCallback']);

        return $mock;
    }

    /** @var mixed[] */
    private $saveNewRecordCallArgs = [];

    public function saveNewRecordCallback() : Payload
    {
        $this->saveNewRecordCallArgs = func_get_args();

        return new Payload(
            Payload::STATUS_CREATED,
            ['message' => 'Foo Bar']
        );
    }
}
