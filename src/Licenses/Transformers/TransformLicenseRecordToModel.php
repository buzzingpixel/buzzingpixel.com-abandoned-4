<?php

declare(strict_types=1);

namespace App\Licenses\Transformers;

use App\Licenses\Models\LicenseModel;
use App\Persistence\Constants;
use App\Persistence\Licenses\LicenseRecord;
use App\Users\Models\UserModel;
use DateTimeImmutable;
use Safe\Exceptions\JsonException;
use function assert;
use function Safe\json_decode;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class TransformLicenseRecordToModel
{
    /**
     * @throws JsonException
     */
    public function __invoke(
        LicenseRecord $record,
        UserModel $ownerUser
    ) : LicenseModel {
        $model = new LicenseModel();

        $model->id = $record->id;

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

        $expires = DateTimeImmutable::createFromFormat(
            Constants::POSTGRES_OUTPUT_FORMAT,
            $record->expires
        );

        assert($expires instanceof DateTimeImmutable);

        $model->expires = $expires;

        return $model;
    }
}
