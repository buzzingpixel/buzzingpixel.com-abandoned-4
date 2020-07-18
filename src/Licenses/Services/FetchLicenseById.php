<?php

declare(strict_types=1);

namespace App\Licenses\Services;

use App\Licenses\Models\LicenseModel;
use App\Licenses\Transformers\TransformLicenseRecordToModel;
use App\Persistence\Licenses\LicenseRecord;
use App\Persistence\RecordQueryFactory;
use App\Users\Models\UserModel;
use Throwable;

use function assert;

class FetchLicenseById
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

    public function __invoke(
        string $id,
        ?UserModel $ownerUser = null
    ): ?LicenseModel {
        try {
            $record = ($this->recordQueryFactory)(
                new LicenseRecord()
            )
                ->withWhere('id', $id)
                ->one();

            if ($record === null) {
                return null;
            }

            assert($record instanceof LicenseRecord);

            return ($this->transformer)($record, $ownerUser);
        } catch (Throwable $e) {
            return null;
        }
    }
}
