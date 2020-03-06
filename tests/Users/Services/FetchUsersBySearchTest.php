<?php

declare(strict_types=1);

namespace Tests\Users\Services;

use App\Persistence\RecordQuery;
use App\Persistence\RecordQueryFactory;
use App\Persistence\Users\UserRecord;
use App\Users\Services\FetchUsersBySearch;
use App\Users\Transformers\TransformUserRecordToUserModel;
use PHPUnit\Framework\TestCase;
use Tests\TestConfig;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class FetchUsersBySearchTest extends TestCase
{
    public function test() : void
    {
        $record1             = new UserRecord();
        $record1->first_name = 'asdf';
        $record1->timezone   = 'America/Chicago';
        $record1->created_at = '2020-01-24 19:59:38+00';

        $record2             = new UserRecord();
        $record2->last_name  = 'stuff';
        $record2->timezone   = 'America/Chicago';
        $record2->created_at = '2020-01-24 19:59:38+00';

        $userRecordDummy = new UserRecord();

        $recordQuery = $this->createMock(RecordQuery::class);

        $index = 0;

        foreach ($userRecordDummy->getSearchableFields() as $field) {
            $recordQuery->expects(self::at($index))
                ->method('withNewWhereGroup')
                ->with(
                    self::equalTo($field),
                    self::equalTo('foo-query-string'),
                    self::equalTo('LIKE')
                )
                ->willReturn($recordQuery);

            $index++;
        }

        $recordQuery->expects(self::at($index))
            ->method('withOrder')
            ->with(
                self::equalTo('last_name'),
                self::equalTo('asc')
            )
            ->willReturn($recordQuery);
        $index++;

        $recordQuery->expects(self::at($index))
            ->method('withOrder')
            ->with(
                self::equalTo('first_name'),
                self::equalTo('asc')
            )
            ->willReturn($recordQuery);
        $index++;

        $recordQuery->expects(self::at($index))
            ->method('withOrder')
            ->with(
                self::equalTo('email_address'),
                self::equalTo('asc')
            )
            ->willReturn($recordQuery);
        $index++;

        $recordQuery->expects(self::at($index))
            ->method('withLimit')
            ->with(self::equalTo(32))
            ->willReturn($recordQuery);
        $index++;

        $recordQuery->expects(self::at($index))
            ->method('withOffset')
            ->with(self::equalTo(54))
            ->willReturn($recordQuery);
        $index++;

        $recordQuery->expects(self::at($index))
            ->method('all')
            ->willReturn([
                $record1,
                $record2,
            ]);

        $recordQueryFactory = $this->createMock(
            RecordQueryFactory::class
        );

        $recordQueryFactory->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (UserRecord $record) use (
                    $recordQuery
                ) {
                    return $recordQuery;
                }
            );

        $service = new FetchUsersBySearch(
            $recordQueryFactory,
            TestConfig::$di->get(
                TransformUserRecordToUserModel::class
            )
        );

        $models = $service('foo-query-string', 32, 54);

        self::assertCount(2, $models);

        $model1 = $models[0];

        self::assertSame(
            'asdf',
            $model1->firstName
        );

        $model2 = $models[1];

        self::assertSame(
            'stuff',
            $model2->lastName
        );
    }
}
