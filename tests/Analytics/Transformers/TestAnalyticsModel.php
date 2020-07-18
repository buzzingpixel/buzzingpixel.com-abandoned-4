<?php

declare(strict_types=1);

namespace Tests\Analytics\Transformers;

use App\Analytics\Models\AnalyticsModel;
use App\Analytics\Transformers\AnalyticsModelToRecord;
use buzzingpixel\cookieapi\Cookie;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class TestAnalyticsModel extends TestCase
{
    public function testSupplemental(): void
    {
        $model = new AnalyticsModel();

        $model->cookie = new Cookie(
            'foo-cookie',
            'foo-cookie-val'
        );

        $transformer = new AnalyticsModelToRecord();

        $record = $transformer($model);

        self::assertSame(
            'foo-cookie-val',
            $record->cookie_id,
        );

        self::assertNull($record->user_id);

        self::assertSame(
            '0',
            $record->logged_in_on_page_load,
        );

        self::assertSame(
            $model->date->format(
                DateTimeInterface::ATOM,
            ),
            $record->date,
        );
    }
}
