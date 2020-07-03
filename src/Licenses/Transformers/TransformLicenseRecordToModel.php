<?php

declare(strict_types=1);

namespace App\Licenses\Transformers;

use App\Licenses\Models\LicenseModel;
use App\Persistence\Constants;
use App\Persistence\Licenses\LicenseRecord;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use Safe\DateTimeImmutable;
use Safe\Exceptions\DatetimeException;
use Safe\Exceptions\JsonException;

use function assert;
use function Safe\json_decode;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class TransformLicenseRecordToModel
{
    private UserApi $userApi;

    public function __construct(UserApi $userApi)
    {
        $this->userApi = $userApi;
    }

    /**
     * @throws JsonException|DatetimeException
     */
    public function __invoke(
        LicenseRecord $record,
        ?UserModel $ownerUser = null
    ): LicenseModel {
        $model = new LicenseModel();

        $model->id = $record->id;

        if ($ownerUser === null || $ownerUser->id !== $record->owner_user_id) {
            $ownerUser = $this->userApi->fetchUserById(
                $record->owner_user_id
            );
        }

        assert($ownerUser instanceof UserModel);

        $model->ownerUser = $ownerUser;

        $model->itemKey = $record->item_key;

        $model->itemTitle = $record->item_title;

        $model->majorVersion = $record->major_version;

        $model->version = $record->version;

        $model->lastAvailableVersion = $record->last_available_version;

        $model->notes = $record->notes;

        /** @psalm-suppress MixedAssignment */
        $model->authorizedDomains = json_decode(
            $record->authorized_domains,
            true
        );

        $model->isDisabled = $record->is_disabled === '1';

        if ($record->expires !== null) {
            $expires = DateTimeImmutable::createFromFormat(
                Constants::POSTGRES_OUTPUT_FORMAT,
                $record->expires
            );

            $model->expires = $expires;
        }

        return $model;
    }
}
