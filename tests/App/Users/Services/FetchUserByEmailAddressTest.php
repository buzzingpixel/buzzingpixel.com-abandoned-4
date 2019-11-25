<?php

declare(strict_types=1);

namespace Tests\App\Users\Services;

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

        self::assertSame('TestId', $userModel->getId());

        self::assertSame('TestEmailAddress', $userModel->getEmailAddress());

        self::assertSame('TestPasswordHash', $userModel->getPasswordHash());

        self::assertSame('', $userModel->getNewPassword());

        self::assertTrue($userModel->isActive());

        self::assertSame('TestFirstName', $userModel->getFirstName());

        self::assertSame('TestLastName', $userModel->getLastName());

        self::assertSame('TestDisplayName', $userModel->getDisplayName());

        self::assertSame('TestBillingName', $userModel->getBillingName());

        self::assertSame('TestBillingCompany', $userModel->getBillingCompany());

        self::assertSame('TestBillingPhone', $userModel->getBillingPhone());

        self::assertSame('TestBillingCountry', $userModel->getBillingCountry());

        self::assertSame('TestBillingAddress', $userModel->getBillingAddress());

        self::assertSame('TestBillingCity', $userModel->getBillingCity());

        self::assertSame('TestBillingPostalCode', $userModel->getBillingPostalCode());

        self::assertSame('2019-11-25T04:11:51+00:00', $userModel->getCreatedAt()->format(DateTimeInterface::ATOM));
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
