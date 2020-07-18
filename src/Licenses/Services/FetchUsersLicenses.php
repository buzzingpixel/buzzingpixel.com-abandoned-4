<?php

declare(strict_types=1);

namespace App\Licenses\Services;

use App\Licenses\Models\LicenseModel;
use App\Licenses\Transformers\TransformLicenseRecordToModel;
use App\Persistence\Licenses\LicenseRecord;
use App\Persistence\RecordQueryFactory;
use App\Users\Models\UserModel;

use function array_map;

class FetchUsersLicenses
{
    private RecordQueryFactory $recordQueryFactory;
    private TransformLicenseRecordToModel $transformer;

    public function __construct(
        RecordQueryFactory $recordQueryFactory,
        TransformLicenseRecordToModel $transformer
    ) {
        $this->recordQueryFactory = $recordQueryFactory;
        $this->transformer        = $transformer;
    }

    /**
     * @return LicenseModel[]
     */
    public function __invoke(UserModel $userModel): array
    {
        $records = ($this->recordQueryFactory)(
            new LicenseRecord()
        )
            ->withWhere('owner_user_id', $userModel->id)
            ->withOrder('item_key', 'asc')
            ->withOrder('major_version', 'desc')
            ->withOrder('version', 'desc')
            ->withOrder('id', 'desc')
            ->all();

        /** @psalm-suppress ArgumentTypeCoercion */
        return array_map(
            fn (LicenseRecord $record) => ($this->transformer)(
                $record,
                $userModel
            ),
            $records
        );
    }
}
