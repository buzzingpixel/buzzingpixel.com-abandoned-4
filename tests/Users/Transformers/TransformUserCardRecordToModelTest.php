<?php

declare(strict_types=1);

namespace Tests\Users\Transformers;

use App\Persistence\Constants;
use App\Persistence\UserCards\UserCardRecord;
use App\Users\Models\UserModel;
use App\Users\Transformers\TransformUserCardRecordToModel;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class TransformUserCardRecordToModelTest extends TestCase
{
    public function test() : void
    {
        $record = new UserCardRecord();

        $record->id = 'foo-id';

        $record->stripe_id = 'foo-stripe-id';

        $record->nickname = 'foo-nickname';

        $record->last_four = 'foo-last-four';

        $record->provider = 'foo-provider';

        $record->is_default = '1';

        $record->expiration = (new DateTimeImmutable())->format(
            Constants::POSTGRES_OUTPUT_FORMAT
        );

        $user = new UserModel();

        $model = (new TransformUserCardRecordToModel())(
            $record,
            $user,
        );

        self::assertSame(
            $record->id,
            $model->id,
        );

        self::assertSame(
            $user,
            $model->user,
        );

        self::assertSame(
            $record->stripe_id,
            $model->stripeId,
        );

        self::assertSame(
            $record->nickname,
            $model->nickname,
        );

        self::assertSame(
            $record->last_four,
            $model->lastFour,
        );

        self::assertSame(
            $record->provider,
            $model->provider,
        );

        self::assertTrue($model->isDefault);

        self::assertSame(
            $record->expiration,
            $model->expiration->format(
                Constants::POSTGRES_OUTPUT_FORMAT
            ),
        );
    }
}
