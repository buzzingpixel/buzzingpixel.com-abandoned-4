<?php

declare(strict_types=1);

namespace App\Software\Services;

use App\Persistence\RecordQueryFactory;
use App\Persistence\Software\SoftwareRecord;
use App\Persistence\Software\SoftwareVersionRecord;
use App\Software\Models\SoftwareModel;
use App\Software\Models\SoftwareVersionModel;
use App\Software\Transformers\TransformSoftwareRecordToModel;
use App\Software\Transformers\TransformSoftwareVersionRecordToModel;
use Throwable;
use function array_map;
use function assert;

class FetchSoftwareById
{
    private RecordQueryFactory $recordQueryFactory;
    private TransformSoftwareRecordToModel $softwareRecordToModel;
    private TransformSoftwareVersionRecordToModel $softwareVersionRecordToModel;

    public function __construct(
        RecordQueryFactory $recordQueryFactory,
        TransformSoftwareRecordToModel $softwareRecordToModel,
        TransformSoftwareVersionRecordToModel $softwareVersionRecordToModel
    ) {
        $this->recordQueryFactory           = $recordQueryFactory;
        $this->softwareRecordToModel        = $softwareRecordToModel;
        $this->softwareVersionRecordToModel = $softwareVersionRecordToModel;
    }

    public function __invoke(string $id) : ?SoftwareModel
    {
        try {
            return $this->innerRun($id);
        } catch (Throwable $e) {
            return null;
        }
    }

    /**
     * @throws Throwable
     */
    private function innerRun(string $id) : ?SoftwareModel
    {
        $softwareRecord = ($this->recordQueryFactory)(
            new SoftwareRecord()
        )
            ->withWhere('id', $id)
            ->one();

        assert(
            $softwareRecord instanceof SoftwareRecord ||
            $softwareRecord === null
        );

        if ($softwareRecord === null) {
            return null;
        }

        /** @var SoftwareVersionRecord[] $versionRecords */
        $versionRecords = ($this->recordQueryFactory)(
            new SoftwareVersionRecord()
        )
            ->withWhere('software_id', $softwareRecord->id)
            ->withOrder('released_on', 'desc')
            ->withOrder('major_version', 'desc')
            ->withOrder('version', 'desc')
            ->all();

        /** @var SoftwareVersionModel[] $versionModels */
        $versionModels = array_map(
            $this->softwareVersionRecordToModel,
            $versionRecords
        );

        return ($this->softwareRecordToModel)(
            $softwareRecord,
            $versionModels,
        );
    }
}
