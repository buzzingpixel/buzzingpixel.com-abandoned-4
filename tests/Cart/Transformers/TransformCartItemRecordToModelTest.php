<?php

declare(strict_types=1);

namespace Tests\Cart\Transformers;

use App\Cart\Transformers\TransformCartItemRecordToModel;
use App\Persistence\Cart\CartItemRecord;
use App\Software\Models\SoftwareModel;
use App\Software\Services\FetchSoftwareBySlug;
use PHPUnit\Framework\TestCase;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class TransformCartItemRecordToModelTest extends TestCase
{
    public function test() : void
    {
        $software = new SoftwareModel();

        $record            = new CartItemRecord();
        $record->id        = 'foo-record-id';
        $record->item_slug = 'foo-item-slug';
        $record->quantity  = '13';

        $fetchSoftwareBySlug = $this->createMock(
            FetchSoftwareBySlug::class
        );

        $fetchSoftwareBySlug->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo('foo-item-slug'))
            ->willReturn($software);

        $transformer = new TransformCartItemRecordToModel(
            $fetchSoftwareBySlug
        );

        $itemModel = $transformer($record);

        self::assertSame(
            'foo-record-id',
            $itemModel->id
        );

        self::assertSame(
            $software,
            $itemModel->software
        );

        self::assertSame(
            13,
            $itemModel->quantity
        );
    }
}
