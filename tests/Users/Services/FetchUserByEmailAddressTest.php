<?php

declare(strict_types=1);

namespace Tests\Users\Services;

use App\Persistence\Users\UserRecord;
use App\Users\Models\UserModel;
use App\Users\Services\FetchUserByEmailAddress;
use App\Users\Transformers\TransformUserRecordToUserModel;
use DateTimeInterface;
use Exception;
use PDO;
use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\TestConfig;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class FetchUserByEmailAddressTest extends TestCase
{
    public function testWhenPdoThrows() : void
    {
        $pdo = $this->createMock(PDO::class);

        $pdo->method('prepare')->willThrowException(new Exception());

        $fetch = new FetchUserByEmailAddress(
            $pdo,
            TestConfig::$di->get(
                TransformUserRecordToUserModel::class
            )
        );

        self::assertNull($fetch('foobar'));
    }

    public function testWhenFetchObjectReturnsNull() : void
    {
        $fetch = new FetchUserByEmailAddress(
            $this->mockPdo(
                $this->mockPdoStatement(
                    'FooBarEmailAddressTest'
                )
            ),
            TestConfig::$di->get(
                TransformUserRecordToUserModel::class
            )
        );

        self::assertNull($fetch('FooBarEmailAddressTest'));
    }

    public function testWhenFetchReturnUserRecord() : void
    {
        $fetch = new FetchUserByEmailAddress(
            $this->mockPdo(
                $this->mockPdoStatement(
                    'BazFooEmail',
                    $this->createUserRecord()
                )
            ),
            TestConfig::$di->get(
                TransformUserRecordToUserModel::class
            )
        );

        $userModel = $fetch('BazFooEmail');

        self::assertInstanceOf(UserModel::class, $userModel);

        self::assertSame('TestId', $userModel->id);

        self::assertSame('TestEmailAddress', $userModel->emailAddress);

        self::assertSame('TestPasswordHash', $userModel->passwordHash);

        self::assertSame('', $userModel->newPassword);

        self::assertTrue($userModel->isActive);

        self::assertSame('TestFirstName', $userModel->firstName);

        self::assertSame('TestLastName', $userModel->lastName);

        self::assertSame('TestDisplayName', $userModel->displayName);

        self::assertSame('TestBillingName', $userModel->billingName);

        self::assertSame('TestBillingCompany', $userModel->billingCompany);

        self::assertSame('TestBillingPhone', $userModel->billingPhone);

        self::assertSame('TestBillingCountry', $userModel->billingCountry);

        self::assertSame('TestBillingAddress', $userModel->billingAddress);

        self::assertSame('TestBillingCity', $userModel->billingCity);

        self::assertSame('TestBillingPostalCode', $userModel->billingPostalCode);

        self::assertSame('2019-11-25T04:11:51+00:00', $userModel->createdAt->format(DateTimeInterface::ATOM));
    }

    private function createUserRecord() : UserRecord
    {
        $userRecord = new UserRecord();

        $userRecord->id = 'TestId';

        $userRecord->email_address = 'TestEmailAddress';

        $userRecord->password_hash = 'TestPasswordHash';

        $userRecord->is_active = '1';

        $userRecord->first_name = 'TestFirstName';

        $userRecord->last_name = 'TestLastName';

        $userRecord->display_name = 'TestDisplayName';

        $userRecord->billing_name = 'TestBillingName';

        $userRecord->billing_company = 'TestBillingCompany';

        $userRecord->billing_phone = 'TestBillingPhone';

        $userRecord->billing_country = 'TestBillingCountry';

        $userRecord->billing_address = 'TestBillingAddress';

        $userRecord->billing_city = 'TestBillingCity';

        $userRecord->billing_postal_code = 'TestBillingPostalCode';

        $userRecord->created_at = '2019-11-25 04:11:51+00';

        $userRecord->timezone = 'America/Chicago';

        return $userRecord;
    }

    /**
     * @return PDOStatement&MockObject
     */
    private function mockPdoStatement(
        string $expectEmailAddress,
        ?UserRecord $userRecord = null
    ) : PDOStatement {
        $mock = $this->createMock(PDOStatement::class);

        $mock->expects(self::at(0))
            ->method('execute')
            ->with(self::equalTo([':email' => $expectEmailAddress]))
            ->willReturn(true);

        $mock->expects(self::at(1))
            ->method('fetchObject')
            ->with(self::equalTo(UserRecord::class))
            ->willReturn($userRecord);

        return $mock;
    }

    /**
     * @return PDO&MockObject
     */
    private function mockPdo(PDOStatement $statement) : PDO
    {
        $mock = $this->createMock(PDO::class);

        $mock->expects(self::once())
            ->method('prepare')
            ->with(
                self::equalTo(
                    'SELECT * FROM users WHERE email_address = :email'
                )
            )
            ->willReturn($statement);

        return $mock;
    }
}
