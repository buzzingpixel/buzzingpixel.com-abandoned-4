<?php

declare(strict_types=1);

namespace App\Software\Services;

use App\Persistence\RecordQueryFactory;
use App\Persistence\Software\SoftwareVersionRecord;
use App\Software\Models\SoftwareVersionModel;
use Throwable;
use function assert;

class FetchSoftwareVersionById
{
    private RecordQueryFactory $recordQueryFactory;
    private FetchSoftwareById $fetchSoftwareById;

    public function __construct(
        RecordQueryFactory $recordQueryFactory,
        FetchSoftwareById $fetchSoftwareById
    ) {
        $this->recordQueryFactory = $recordQueryFactory;
        $this->fetchSoftwareById  = $fetchSoftwareById;
    }

    public function __invoke(string $id) : ?SoftwareVersionModel
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
    private function innerRun(string $id) : ?SoftwareVersionModel
    {
        $softwareVersionRecord = ($this->recordQueryFactory)(
            new SoftwareVersionRecord()
        )
            ->withWhere('id', $id)
            ->one();
        assert($softwareVersionRecord instanceof SoftwareVersionRecord || $softwareVersionRecord === null);

        if ($softwareVersionRecord === null) {
            return null;
        }

        $software = ($this->fetchSoftwareById)(
            $softwareVersionRecord->software_id
        );

        if ($software === null) {
            return null;
        }

        $softwareVersionModel = null;

        foreach ($software->getVersions() as $version) {
            if ($version->getId() !== $softwareVersionRecord->id) {
                continue;
            }

            $softwareVersionModel = $version;

            break;
        }

        return $softwareVersionModel;
    }
}
