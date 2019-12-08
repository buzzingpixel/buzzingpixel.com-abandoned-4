<?php

declare(strict_types=1);

namespace App\Software\Services;

use App\Persistence\RecordQueryFactory;
use App\Persistence\Software\SoftwareRecord;
use App\Persistence\Software\SoftwareVersionRecord;
use App\Software\Models\SoftwareModel;
use App\Software\Transformers\TransformSoftwareRecordToModel;
use App\Software\Transformers\TransformSoftwareVersionRecordToModel;
use function array_map;

class FetchAllSoftware
{
    /** @var RecordQueryFactory */
    private $recordQueryFactory;
    /** @var TransformSoftwareRecordToModel */
    private $softwareRecordToModel;
    /** @var TransformSoftwareVersionRecordToModel */
    private $softwareVersionRecordToModel;

    public function __construct(
        RecordQueryFactory $recordQueryFactory,
        TransformSoftwareRecordToModel $softwareRecordToModel,
        TransformSoftwareVersionRecordToModel $softwareVersionRecordToModel
    ) {
        $this->recordQueryFactory           = $recordQueryFactory;
        $this->softwareRecordToModel        = $softwareRecordToModel;
        $this->softwareVersionRecordToModel = $softwareVersionRecordToModel;
    }

    /**
     * @return SoftwareModel[]
     */
    public function __invoke() : array
    {
        /** @var SoftwareRecord[] $records */
        $records = ($this->recordQueryFactory)(
            new SoftwareRecord()
        )
            ->withOrder('name', 'asc')
            ->all();

        $softwareIds = array_map(
            static function (SoftwareRecord $record) {
                return $record->id;
            },
            $records
        );

        /** @var SoftwareVersionRecord[] $versionRecords */
        $versionRecords = ($this->recordQueryFactory)(
            new SoftwareVersionRecord()
        )
            ->withWhere(
                'software_id',
                $softwareIds,
                'IN'
            )
            ->withOrder('released_on', 'desc')
            ->withOrder('major_version', 'desc')
            ->withOrder('version', 'desc')
            ->all();

        $versionModelsKeyed = [];

        foreach ($versionRecords as $versionRecord) {
            $id = $versionRecord->software_id;

            $versionModelsKeyed[$id][] = ($this->softwareVersionRecordToModel)(
                $versionRecord
            );
        }

        return array_map(
            function (SoftwareRecord $record) use (
                $versionModelsKeyed
            ) {
                return ($this->softwareRecordToModel)(
                    $record,
                    $versionModelsKeyed[$record->id] ?? []
                );
            },
            $records
        );
    }
}
