<?php

declare(strict_types=1);

namespace Tests\Persistence;

use App\Payload\Payload;
use App\Persistence\SaveExistingRecord;
use App\Persistence\Users\UserRecord;
use Exception;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use function implode;

class SaveExistingRecordTest extends TestCase
{
    public function testWhenIdIsMissing() : void
    {
        $record = new UserRecord();

        $pdo = $this->createMock(PDO::class);

        $payload = (new SaveExistingRecord($pdo))($record);

        self::assertSame(
            Payload::STATUS_NOT_UPDATED,
            $payload->getStatus()
        );

        self::assertSame(
            ['message' => 'A record ID is required'],
            $payload->getResult()
        );
    }

    public function testWhenPdoThrows() : void
    {
        $record = new UserRecord();

        $record->id = 'TestId';

        $pdo = $this->createMock(PDO::class);

        $pdo->method('prepare')->willThrowException(new Exception());

        $payload = (new SaveExistingRecord($pdo))($record);

        self::assertSame(
            Payload::STATUS_NOT_UPDATED,
            $payload->getStatus()
        );

        self::assertSame(
            ['message' => 'An unknown error occurred'],
            $payload->getResult()
        );
    }

    public function testWhenStatementIsNotSuccessful() : void
    {
        $record = new UserRecord();

        $record->id = 'TestId';

        $statement = $this->createMock(PDOStatement::class);

        $statement->method('execute')->willReturn(false);

        $pdo = $this->createMock(PDO::class);

        $pdo->method('prepare')->willReturn($statement);

        $payload = (new SaveExistingRecord($pdo))($record);

        self::assertSame(
            Payload::STATUS_NOT_UPDATED,
            $payload->getStatus()
        );

        self::assertSame(
            ['message' => 'An unknown error occurred'],
            $payload->getResult()
        );
    }

    public function test() : void
    {
        $userRecord = new UserRecord();

        $userRecord->id = 'TestId';

        $userRecord->email_address = 'TestEmailAddress';

        $userRecord->password_hash = 'TestPasswordHash';

        $userRecord->is_active = '1';

        $userRecord->first_name = '';

        $userRecord->last_name = 'TestLastName';

        $userRecord->display_name = 'TestDisplayName';

        $userRecord->billing_name = '';

        $userRecord->billing_company = 'TestBillingCompany';

        $userRecord->billing_phone = 'TestBillingPhone';

        $userRecord->billing_country = 'TestBillingCountry';

        $userRecord->billing_address = 'TestBillingAddress';

        $userRecord->billing_city = 'TestBillingCity';

        $userRecord->billing_postal_code = 'TestBillingPostalCode';

        $userRecord->created_at = '2019-11-25 04:11:51+00';

        $into = implode(', ', $userRecord->getFields());

        $values = implode(', ', $userRecord->getFields(true));

        $statement = $this->createMock(PDOStatement::class);

        $statement->expects(self::once())
            ->method('execute')
            ->with(self::equalTo($userRecord->getBindValues()))
            ->willReturn(true);

        $setSql = [];

        foreach ($userRecord->getFields() as $field) {
            if ($field === 'id') {
                continue;
            }

            $setSql[] = $field . '=:' . $field;
        }

        $sql = 'UPDATE users SET ' .
            implode(', ', $setSql) .
            ' WHERE id=:id';

        $pdo = $this->createMock(PDO::class);

        $pdo->expects(self::once())
            ->method('prepare')
            ->with(self::equalTo($sql))
            ->willReturn($statement);

        $payload = (new SaveExistingRecord($pdo))($userRecord);

        self::assertSame(
            Payload::STATUS_UPDATED,
            $payload->getStatus()
        );
    }
}
