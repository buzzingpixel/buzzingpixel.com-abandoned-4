<?php

declare(strict_types=1);

namespace Tests\Licenses\Transformers;

use App\Licenses\Models\LicenseModel;
use App\Licenses\Transformers\TransformLicenseModelToRecord;
use PHPUnit\Framework\TestCase;
use Throwable;
use function Safe\json_encode;

class TransformLicenseModelToRecordTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testTransformer() : void
    {
        $model = new LicenseModel();

        $model->id = 'fooId';

        $model->itemKey = 'voyager';

        $model->itemTitle = 'Voyager';

        $model->majorVersion = '4';

        $model->version = '4.5.6';

        $model->lastAvailableVersion = '4.8.2';

        $model->notes = 'Janeway';

        $model->authorizedDomains = ['starfleet.com', 'federation.com'];

        $model->isDisabled = true;

        $transformer = new TransformLicenseModelToRecord();

        $record = $transformer($model);

        self::assertSame(
            $model->id,
            $record->id,
        );

        self::assertSame(
            $model->itemKey,
            $record->item_key,
        );

        self::assertSame(
            $model->itemTitle,
            $record->item_title,
        );

        self::assertSame(
            $model->majorVersion,
            $record->major_version,
        );

        self::assertSame(
            $model->version,
            $record->version,
        );

        self::assertSame(
            $model->lastAvailableVersion,
            $record->last_available_version,
        );

        self::assertSame(
            $model->notes,
            $record->notes,
        );

        self::assertSame(
            json_encode($model->authorizedDomains),
            $record->authorized_domains
        );

        self::assertSame(
            '1',
            $record->is_disabled,
        );
    }
}
