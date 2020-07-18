<?php

declare(strict_types=1);

namespace Tests\Analytics\Services;

use App\Analytics\Models\AnalyticsModel;
use App\Analytics\Services\CreatePageView;
use App\Analytics\Transformers\AnalyticsModelToRecord;
use App\Payload\Payload;
use App\Persistence\Analytics\AnalyticsRecord;
use App\Persistence\SaveNewRecord;
use App\Persistence\UuidFactoryWithOrderedTimeCodec;
use App\Users\Models\UserModel;
use buzzingpixel\cookieapi\Cookie;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Tests\TestConfig;
use Throwable;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

class CreatePageViewTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test(): void
    {
        $payload = new Payload(Payload::STATUS_CREATED);

        $model = new AnalyticsModel();

        $model->cookie = new Cookie(
            'foo-cookie',
            'foo-cookie-val'
        );

        $model->user     = new UserModel();
        $model->user->id = 'foo-user-id';

        $model->wasLoggedInOnPageLoad = true;

        $model->uri = '/foo/uri';

        $uuid = TestConfig::$di->get(UuidFactoryWithOrderedTimeCodec::class)
            ->uuid1();

        $uuidFactory = $this->createMock(
            UuidFactoryWithOrderedTimeCodec::class,
        );

        $uuidFactory->method('uuid1')
            ->willReturn($uuid);

        $saveNewRecord = $this->createMock(
            SaveNewRecord::class
        );

        $saveNewRecord->expects(self::once())
            ->method('__invoke')
            ->willReturnCallback(
                static function (AnalyticsRecord $record) use (
                    $payload,
                    $model
                ): Payload {
                    self::assertSame(
                        'foo-cookie-val',
                        $record->cookie_id,
                    );

                    self::assertSame(
                        'foo-user-id',
                        $record->user_id,
                    );

                    self::assertSame(
                        '1',
                        $record->logged_in_on_page_load
                    );

                    self::assertSame(
                        '/foo/uri',
                        $record->uri,
                    );

                    self::assertSame(
                        $model->date->format(
                            DateTimeInterface::ATOM,
                        ),
                        $record->date,
                    );

                    return $payload;
                }
            );

        $service = new CreatePageView(
            $uuidFactory,
            TestConfig::$di->get(
                AnalyticsModelToRecord::class,
            ),
            $saveNewRecord,
        );

        self::assertSame(
            $payload,
            $service($model),
        );
    }
}
