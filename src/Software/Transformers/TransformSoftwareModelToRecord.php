<?php

declare(strict_types=1);

namespace App\Software\Transformers;

use App\Persistence\Software\SoftwareRecord;
use App\Software\Models\SoftwareModel;

class TransformSoftwareModelToRecord
{
    public function __invoke(SoftwareModel $model) : SoftwareRecord
    {
        $record = new SoftwareRecord();

        $record->id = $model->getId();

        $record->slug = $model->getSlug();

        $record->name = $model->getName();

        $record->is_for_sale = $model->isForSale() ? '1' : '0';

        return $record;
    }
}
