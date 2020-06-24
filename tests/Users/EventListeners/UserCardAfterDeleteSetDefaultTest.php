<?php

declare(strict_types=1);

namespace Tests\Users\EventListeners;

use App\Payload\Payload;
use App\Users\EventListeners\UserCardAfterDeleteSetDefault;
use App\Users\Events\DeleteUserCardAfterDelete;
use App\Users\Models\UserCardModel;
use App\Users\Models\UserModel;
use App\Users\UserApi;
use PHPUnit\Framework\TestCase;

class UserCardAfterDeleteSetDefaultTest extends TestCase
{
    public function testWhenDeletedCardNotDefault(): void
    {
        $user = new UserModel();

        $card = new UserCardModel();

        $card->user = $user;

        $afterDelete = new DeleteUserCardAfterDelete($card);

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::never())
            ->method(self::anything());

        $listener = new UserCardAfterDeleteSetDefault($userApi);

        $listener->onAfterDeleteUserCard($afterDelete);
    }

    public function testWhenNoOtherUserCards(): void
    {
        $user = new UserModel();

        $card = new UserCardModel();

        $card->user = $user;

        $card->isDefault = true;

        $afterDelete = new DeleteUserCardAfterDelete($card);

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::once())
            ->method('fetchUserCards')
            ->with(self::equalTo($user))
            ->willReturn([]);

        $listener = new UserCardAfterDeleteSetDefault($userApi);

        $listener->onAfterDeleteUserCard($afterDelete);
    }

    public function test(): void
    {
        $user = new UserModel();

        $card = new UserCardModel();

        $card->user = $user;

        $card->isDefault = true;

        $otherCard1 = new UserCardModel();

        $otherCard1->user = $user;

        $otherCard2 = new UserCardModel();

        $otherCard2->user = $user;

        $afterDelete = new DeleteUserCardAfterDelete($card);

        $userApi = $this->createMock(UserApi::class);

        $userApi->expects(self::at(0))
            ->method('fetchUserCards')
            ->with(self::equalTo($user))
            ->willReturn([$otherCard1, $otherCard2]);

        $userApi->expects(self::at(1))
            ->method('saveUserCard')
            ->willReturnCallback(static function (
                UserCardModel $card
            ) use ($otherCard1): Payload {
                self::assertSame($otherCard1, $card);

                self::assertTrue($card->isDefault);

                return new Payload(Payload::STATUS_UPDATED);
            });

        $listener = new UserCardAfterDeleteSetDefault($userApi);

        $listener->onAfterDeleteUserCard($afterDelete);
    }
}
