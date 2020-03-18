<?php

declare(strict_types=1);

namespace Tests\Orders\Transformers;

use App\Licenses\LicenseApi;
use App\Licenses\Models\LicenseModel;
use App\Orders\Transformers\TransformOrderItemRecordToModel;
use App\Persistence\Orders\OrderItemRecord;
use PHPUnit\Framework\TestCase;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class TransformOrderItemRecordToModelTest extends TestCase
{
    public function test() : void
    {
        $record                    = new OrderItemRecord();
        $record->id                = 'foo-item-id';
        $record->license_id        = 'foo-license-id';
        $record->item_key          = 'foo-item-key';
        $record->item_title        = 'foo-item-title';
        $record->major_version     = 'foo-major-version';
        $record->version           = 'foo-version';
        $record->price             = '123.4';
        $record->original_price    = '345.6';
        $record->is_upgrade        = '1';
        $record->has_been_upgraded = 'true';

        $license = new LicenseModel();

        $licenseApi = $this->createMock(LicenseApi::class);

        $licenseApi->expects(self::once())
            ->method('fetchLicenseById')
            ->with(self::equalTo('foo-license-id'))
            ->willReturn($license);

        $transformer = new TransformOrderItemRecordToModel(
            $licenseApi
        );

        $model = $transformer($record);

        self::assertSame('foo-item-id', $model->id);

        self::assertSame($license, $model->license);

        self::assertSame('foo-item-key', $model->itemKey);

        self::assertSame('foo-item-title', $model->itemTitle);

        self::assertSame('foo-major-version', $model->majorVersion);

        self::assertSame('foo-version', $model->version);

        self::assertSame(123.4, $model->price);

        self::assertSame(345.6, $model->originalPrice);

        self::assertTrue($model->isUpgrade);

        self::assertTrue($model->hasBeenUpgraded);
    }
}
