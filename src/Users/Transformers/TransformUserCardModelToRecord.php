<?php

declare(strict_types=1);

namespace App\Users\Transformers;

use App\Persistence\UserCards\UserCardRecord;
use App\Users\Models\UserCardModel;
use DateTimeInterface;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class TransformUserCardModelToRecord
{
    public function __invoke(UserCardModel $model) : UserCardRecord
    {
        $record = new UserCardRecord();

        $record->id = $model->id;

        $record->user_id = $model->user->id;

        $record->stripe_id = $model->stripeId;

        $record->nickname = $model->nickname;

        $record->last_four = $model->lastFour;

        $record->provider = $model->provider;

        $record->name_on_card = $model->nameOnCard;

        $record->address = $model->address;

        $record->address2 = $model->address2;

        $record->city = $model->city;

        $record->state = $model->state;

        $record->postal_code = $model->postalCode;

        $record->country = $model->country;

        $record->is_default = $model->isDefault ? '1' : '0';

        $record->expiration = $model->expiration->format(
            DateTimeInterface::ATOM
        );

        return $record;
    }
}
