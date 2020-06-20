<?php

declare(strict_types=1);

namespace App\Users\Transformers;

use App\Persistence\Constants;
use App\Persistence\UserCards\UserCardRecord;
use App\Users\Models\UserCardModel;
use App\Users\Models\UserModel;
use DateTimeImmutable;
use function assert;
use function in_array;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class TransformUserCardRecordToModel
{
    public function __invoke(
        UserCardRecord $record,
        UserModel $user
    ) : UserCardModel {
        $model = new UserCardModel();

        $model->id = $record->id;

        $model->user = $user;

        $model->stripeId = $record->stripe_id;

        $model->nickname = $record->nickname;

        $model->lastFour = $record->last_four;

        $model->provider = $record->provider;

        $model->nameOnCard = $record->name_on_card;

        $model->address = $record->address;

        $model->address2 = $record->address2;

        $model->city = $record->city;

        $model->state = $record->state;

        $model->postalCode = $record->postal_code;

        $model->country = $record->country;

        $model->isDefault = in_array(
            $record->is_default,
            ['1', 1, true],
            true
        );

        $expiration = DateTimeImmutable::createFromFormat(
            Constants::POSTGRES_OUTPUT_FORMAT,
            $record->expiration
        );

        assert($expiration instanceof DateTimeImmutable);

        $model->expiration = $expiration;

        return $model;
    }
}
