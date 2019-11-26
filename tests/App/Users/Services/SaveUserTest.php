<?php

declare(strict_types=1);

namespace Tests\App\Users\Services;

use App\Payload\Payload;
use App\Persistence\SaveExistingRecord;
use App\Persistence\SaveNewRecord;
use App\Persistence\Users\UserRecord;
use App\Persistence\UuidFactoryWithOrderedTimeCodec;
use App\Users\Models\UserModel;
use App\Users\Services\FetchUserByEmailAddress;
use App\Users\Services\FetchUserById;
use App\Users\Services\SaveUser;
use App\Users\Transformers\TransformUserModelToUserRecord;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\TestConfig;
use Throwable;
use function func_get_args;
use function password_verify;

class SaveUserTest extends TestCase
{
    /** @var SaveUser */
    private $saveUser;

    /** @var string */
    private $expectFetchUserEmailAddress = '';
    /** @var bool */
    private $fetchUserByEmailAddressReturnsUser = false;
    /** @var bool */
    private $expectNewRecordCall = false;
    /** @var string */
    private $expectFetchUserId = '';
    /** @var bool */
    private $fetchUserByIdReturnsUser = false;
    /** @var bool */
    private $expectExistingRecordCall = false;

    public function testWhenNoPasswordHashAndNoNewPassword() : void
    {
        $this->expectFetchUserEmailAddress        = '';
        $this->fetchUserByEmailAddressReturnsUser = false;
        $this->expectNewRecordCall                = false;
        $this->expectFetchUserId                  = '';
        $this->fetchUserByIdReturnsUser           = false;
        $this->expectExistingRecordCall           = false;

        $this->internalSetup();

        $userModel = new UserModel();

        $payload = ($this->saveUser)($userModel);

        self::assertSame(
            Payload::STATUS_NOT_VALID,
            $payload->getStatus()
        );

        self::assertSame(
            [
                'password' => 'Password is required',
                'emailAddress' => 'A valid email address is required',
            ],
            $payload->getResult()
        );
    }

    public function testWhenPasswordHashAndInvalidEmailAddress() : void
    {
        $this->expectFetchUserEmailAddress        = '';
        $this->fetchUserByEmailAddressReturnsUser = false;
        $this->expectNewRecordCall                = false;
        $this->expectFetchUserId                  = '';
        $this->fetchUserByIdReturnsUser           = false;
        $this->expectExistingRecordCall           = false;

        $this->internalSetup();

        $userModel = new UserModel([
            'emailAddress' => 'foobar',
            'passwordHash' => 'TestPasswordHash',
        ]);

        $payload = ($this->saveUser)($userModel);

        self::assertSame(
            Payload::STATUS_NOT_VALID,
            $payload->getStatus()
        );

        self::assertSame(
            ['emailAddress' => 'A valid email address is required'],
            $payload->getResult()
        );
    }

    public function testWhenNewPasswordIsTooShort() : void
    {
        $this->expectFetchUserEmailAddress        = '';
        $this->fetchUserByEmailAddressReturnsUser = false;
        $this->expectNewRecordCall                = false;
        $this->expectFetchUserId                  = '';
        $this->fetchUserByIdReturnsUser           = false;
        $this->expectExistingRecordCall           = false;

        $this->internalSetup();

        $userModel = new UserModel([
            'emailAddress' => 'foo@bar.baz',
            'newPassword' => 'foo',
        ]);

        $payload = ($this->saveUser)($userModel);

        self::assertSame(
            Payload::STATUS_NOT_VALID,
            $payload->getStatus()
        );

        self::assertSame(
            ['password' => 'Password is too short'],
            $payload->getResult()
        );
    }

    public function testSaveNewUserWhenEmailExists() : void
    {
        $this->expectFetchUserEmailAddress        = 'foo@bar.baz';
        $this->fetchUserByEmailAddressReturnsUser = true;
        $this->expectNewRecordCall                = false;
        $this->expectFetchUserId                  = '';
        $this->fetchUserByIdReturnsUser           = false;
        $this->expectExistingRecordCall           = false;

        $this->internalSetup();

        $userModel = new UserModel([
            'emailAddress' => 'foo@bar.baz',
            'newPassword' => 'FooBarBaz',
            'isActive' => false,
            'firstName' => 'TestFirstName',
            'lastName' => 'TestLastName',
            'displayName' => 'TestDisplayName',
            'billingName' => 'TestBillingName',
            'billingCompany' => 'TestBillingCompany',
            'billingPhone' => 'TestBillingPhone',
            'billingCountry' => 'TestBillingCountry',
            'billingAddress' => 'TestBillingAddress',
            'billingCity' => 'TestBillingCity',
            'billingPostalCode' => 'TestBillingPostalCode',
        ]);

        $payload = ($this->saveUser)($userModel);

        self::assertSame(
            Payload::STATUS_NOT_CREATED,
            $payload->getStatus()
        );

        self::assertSame(
            ['message' => 'User with email address foo@bar.baz already exists'],
            $payload->getResult()
        );
    }

    public function testSaveNewUser() : void
    {
        $this->expectFetchUserEmailAddress        = 'foo@bar.baz';
        $this->fetchUserByEmailAddressReturnsUser = false;
        $this->expectNewRecordCall                = true;
        $this->expectFetchUserId                  = '';
        $this->fetchUserByIdReturnsUser           = false;
        $this->expectExistingRecordCall           = false;

        $this->internalSetup();

        $userModel = new UserModel([
            'emailAddress' => 'foo@bar.baz',
            'newPassword' => 'FooBarBaz',
            'isActive' => false,
            'firstName' => 'TestFirstName',
            'lastName' => 'TestLastName',
            'displayName' => 'TestDisplayName',
            'billingName' => 'TestBillingName',
            'billingCompany' => 'TestBillingCompany',
            'billingPhone' => 'TestBillingPhone',
            'billingCountry' => 'TestBillingCountry',
            'billingAddress' => 'TestBillingAddress',
            'billingCity' => 'TestBillingCity',
            'billingPostalCode' => 'TestBillingPostalCode',
        ]);

        $payload = ($this->saveUser)($userModel);

        self::assertSame(
            Payload::STATUS_CREATED,
            $payload->getStatus()
        );

        self::assertCount(1, $this->newRecordCallArgs);

        /** @var UserRecord|null $userRecord */
        $userRecord = $this->newRecordCallArgs[0];
        self::assertInstanceOf(UserRecord::class, $userRecord);

        self::assertSame('foo@bar.baz', $userRecord->email_address);

        self::assertSame('0', $userRecord->is_active);

        self::assertSame('TestFirstName', $userRecord->first_name);

        self::assertSame('TestLastName', $userRecord->last_name);

        self::assertSame('TestDisplayName', $userRecord->display_name);

        self::assertSame('TestBillingName', $userRecord->billing_name);

        self::assertSame('TestBillingCompany', $userRecord->billing_company);

        self::assertSame('TestBillingPhone', $userRecord->billing_phone);

        self::assertSame('TestBillingCountry', $userRecord->billing_country);

        self::assertSame('TestBillingAddress', $userRecord->billing_address);

        self::assertSame('TestBillingCity', $userRecord->billing_city);

        self::assertSame('TestBillingPostalCode', $userRecord->billing_postal_code);

        self::assertNotEmpty($userRecord->created_at);

        self::assertNotEmpty($userRecord->id);

        self::assertTrue(password_verify('FooBarBaz', $userRecord->password_hash));

        self::assertFalse(password_verify('BazBarFoo', $userRecord->password_hash));
    }

    /**
     * @throws Throwable
     */
    public function testSaveExistingUserWhenIdDoesNotExist() : void
    {
        $testId = TestConfig::$di->get(UuidFactoryWithOrderedTimeCodec::class)->uuid1()->toString();

        $this->expectFetchUserEmailAddress        = '';
        $this->fetchUserByEmailAddressReturnsUser = false;
        $this->expectNewRecordCall                = false;
        $this->expectFetchUserId                  = $testId;
        $this->fetchUserByIdReturnsUser           = false;
        $this->expectExistingRecordCall           = false;

        $this->internalSetup();

        $userModel = new UserModel([
            'id' => $testId,
            'emailAddress' => 'foo@bar.baz',
            'newPassword' => 'BazFooBar ',
            'isActive' => true,
            'firstName' => 'TestFirstName',
            'lastName' => 'TestLastName',
            'displayName' => 'TestDisplayName',
            'billingName' => 'TestBillingName',
            'billingCompany' => 'TestBillingCompany',
            'billingPhone' => 'TestBillingPhone',
            'billingCountry' => 'TestBillingCountry',
            'billingAddress' => 'TestBillingAddress',
            'billingCity' => 'TestBillingCity',
            'billingPostalCode' => 'TestBillingPostalCode',
        ]);

        $payload = ($this->saveUser)($userModel);

        self::assertSame(
            Payload::STATUS_NOT_FOUND,
            $payload->getStatus()
        );

        self::assertSame(
            ['message' => 'User with id ' . $testId . ' not found'],
            $payload->getResult()
        );
    }

    /**
     * @throws Throwable
     */
    public function testSaveExistingUser() : void
    {
        $testId = TestConfig::$di->get(UuidFactoryWithOrderedTimeCodec::class)->uuid1()->toString();

        $this->expectFetchUserEmailAddress        = '';
        $this->fetchUserByEmailAddressReturnsUser = false;
        $this->expectNewRecordCall                = false;
        $this->expectFetchUserId                  = $testId;
        $this->fetchUserByIdReturnsUser           = true;
        $this->expectExistingRecordCall           = true;

        $this->internalSetup();

        $createdAt = new DateTimeImmutable('now', new DateTimeZone('UTC'));

        $userModel = new UserModel([
            'id' => $testId,
            'emailAddress' => 'foo@bar.baz',
            'passwordHash' => 'ExistingFooBarPassHash',
            'isActive' => true,
            'firstName' => 'ExistingFirstName',
            'lastName' => 'ExistingLastName',
            'displayName' => 'ExistingDisplayName',
            'billingName' => 'ExistingBillingName',
            'billingCompany' => 'ExistingBillingCompany',
            'billingPhone' => 'ExistingBillingPhone',
            'billingCountry' => 'ExistingBillingCountry',
            'billingAddress' => 'ExistingBillingAddress',
            'billingCity' => 'ExistingBillingCity',
            'billingPostalCode' => 'ExistingBillingPostalCode',
            'createdAt' => $createdAt,
        ]);

        $payload = ($this->saveUser)($userModel);

        self::assertSame(
            Payload::STATUS_UPDATED,
            $payload->getStatus()
        );

        self::assertCount(1, $this->existingRecordCallArgs);

        /** @var UserRecord|null $userRecord */
        $userRecord = $this->existingRecordCallArgs[0];
        self::assertInstanceOf(UserRecord::class, $userRecord);

        self::assertSame('foo@bar.baz', $userRecord->email_address);

        self::assertSame('1', $userRecord->is_active);

        self::assertSame('ExistingFirstName', $userRecord->first_name);

        self::assertSame('ExistingLastName', $userRecord->last_name);

        self::assertSame('ExistingDisplayName', $userRecord->display_name);

        self::assertSame('ExistingBillingName', $userRecord->billing_name);

        self::assertSame('ExistingBillingCompany', $userRecord->billing_company);

        self::assertSame('ExistingBillingPhone', $userRecord->billing_phone);

        self::assertSame('ExistingBillingCountry', $userRecord->billing_country);

        self::assertSame('ExistingBillingAddress', $userRecord->billing_address);

        self::assertSame('ExistingBillingCity', $userRecord->billing_city);

        self::assertSame('ExistingBillingPostalCode', $userRecord->billing_postal_code);

        self::assertSame($testId, $userRecord->id);

        self::assertSame('ExistingFooBarPassHash', $userRecord->password_hash);

        self::assertSame($createdAt->format(DateTimeInterface::ATOM), $userRecord->created_at);
    }

    private function internalSetup() : void
    {
        $this->saveUser = new SaveUser(
            $this->mockFetchUserByEmailAddress(),
            $this->mockSaveNewRecord(),
            $this->mockFetchUserById(),
            $this->mockSaveExistingRecord(),
            TestConfig::$di->get(
                TransformUserModelToUserRecord::class
            ),
            TestConfig::$di->get(
                UuidFactoryWithOrderedTimeCodec::class
            )
        );
    }

    /**
     * @return FetchUserByEmailAddress&MockObject
     */
    private function mockFetchUserByEmailAddress() : FetchUserByEmailAddress
    {
        $mock = $this->createMock(
            FetchUserByEmailAddress::class
        );

        if ($this->expectFetchUserEmailAddress === '') {
            $mock->expects(self::never())
                ->method(self::anything());

            return $mock;
        }

        $mock->expects(self::once())
            ->method('__invoke')
            ->with(
                self::equalTo($this->expectFetchUserEmailAddress)
            )
            ->willReturn(
                $this->fetchUserByEmailAddressReturnsUser ?
                    new UserModel() :
                    null
            );

        return $mock;
    }

    /** @var mixed[] */
    private $newRecordCallArgs = [];

    /**
     * @return SaveNewRecord&MockObject
     */
    private function mockSaveNewRecord() : SaveNewRecord
    {
        $this->newRecordCallArgs = [];

        $mock = $this->createMock(
            SaveNewRecord::class
        );

        if (! $this->expectNewRecordCall) {
            $mock->expects(self::never())
                ->method(self::anything());

            return $mock;
        }

        $mock->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(function () {
                $this->newRecordCallArgs = func_get_args();

                return new Payload(Payload::STATUS_CREATED);
            });

        return $mock;
    }

    /**
     * @return FetchUserById&MockObject
     */
    private function mockFetchUserById() : FetchUserById
    {
        $mock = $this->createMock(
            FetchUserById::class
        );

        if ($this->expectFetchUserId === '') {
            $mock->expects(self::never())
                ->method(self::anything());

            return $mock;
        }

        $mock->expects(self::once())
            ->method('__invoke')
            ->with(
                self::equalTo($this->expectFetchUserId)
            )
            ->willReturn(
                $this->fetchUserByIdReturnsUser ?
                    new UserModel() :
                    null
            );

        return $mock;
    }

    /** @var mixed[] */
    private $existingRecordCallArgs = [];

    /**
     * @return SaveExistingRecord&MockObject
     */
    private function mockSaveExistingRecord() : SaveExistingRecord
    {
        $this->existingRecordCallArgs = [];

        $mock = $this->createMock(
            SaveExistingRecord::class
        );

        if (! $this->expectExistingRecordCall) {
            $mock->expects(self::never())
                ->method(self::anything());

            return $mock;
        }

        $mock->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(function () {
                $this->existingRecordCallArgs = func_get_args();

                return new Payload(Payload::STATUS_UPDATED);
            });

        return $mock;
    }
}
