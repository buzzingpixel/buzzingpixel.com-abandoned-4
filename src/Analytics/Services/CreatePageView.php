<?php

declare(strict_types=1);

namespace App\Analytics\Services;

use App\Analytics\Models\AnalyticsModel;
use App\Analytics\Transformers\AnalyticsModelToRecord;
use App\Payload\Payload;
use App\Persistence\SaveNewRecord;
use App\Persistence\UuidFactoryWithOrderedTimeCodec;

class CreatePageView
{
    private UuidFactoryWithOrderedTimeCodec $uuidFactory;
    private AnalyticsModelToRecord $modelToRecord;
    private SaveNewRecord $saveNewRecord;

    public function __construct(
        UuidFactoryWithOrderedTimeCodec $uuidFactory,
        AnalyticsModelToRecord $modelToRecord,
        SaveNewRecord $saveNewRecord
    ) {
        $this->uuidFactory   = $uuidFactory;
        $this->modelToRecord = $modelToRecord;
        $this->saveNewRecord = $saveNewRecord;
    }

    public function __invoke(AnalyticsModel $model): Payload
    {
        $record = ($this->modelToRecord)($model);

        /** @noinspection PhpUnhandledExceptionInspection */
        $record->id = $this->uuidFactory->uuid1()->toString();

        return ($this->saveNewRecord)($record);
    }
}
