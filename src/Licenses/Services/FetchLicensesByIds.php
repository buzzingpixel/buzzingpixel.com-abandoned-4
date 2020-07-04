<?php

declare(strict_types=1);

namespace App\Licenses\Services;

use App\Licenses\Models\LicenseModel;
use App\Licenses\Transformers\TransformLicenseRecordToModel;
use App\Persistence\Licenses\LicenseRecord;
use App\Persistence\RecordQueryFactory;
use Throwable;

use function array_map;
use function count;

class FetchLicensesByIds
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
     * @param string[] $ids
     *
     * @return LicenseModel[]
     */
    public function __invoke(array $ids): array
    {
        try {
            if (count($ids) < 1) {
                return [];
            }

            $records = ($this->recordQueryFactory)(
                new LicenseRecord()
            )
                ->withWhere('id', $ids, 'IN')
                ->withOrder('item_key', 'asc')
                ->withOrder('major_version', 'desc')
                ->withOrder('version', 'desc')
                ->withOrder('id', 'desc')
                ->all();

            /** @psalm-suppress ArgumentTypeCoercion */
            return array_map(
                fn (LicenseRecord $record) => ($this->transformer)(
                    $record
                ),
                $records
            );
        } catch (Throwable $e) {
            return [];
        }
    }
}
