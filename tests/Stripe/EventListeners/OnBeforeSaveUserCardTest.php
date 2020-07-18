<?php

declare(strict_types=1);

namespace Tests\Stripe\EventListeners;

use _HumbugBox89320708a2e3\Nette\Neon\Exception;
use App\Stripe\EventListeners\OnBeforeSaveUserCard;
use App\Stripe\Services\SaveExistingCard;
use App\Stripe\Services\SaveNewCard;
use App\Stripe\Services\UpdateStripeCustomer;
use App\Users\Events\SaveUserCardBeforeSave;
use App\Users\Models\UserCardModel;
use App\Users\Models\UserModel;
use PHPUnit\Framework\TestCase;

class OnBeforeSaveUserCardTest extends TestCase
{
    public function testWhenThrows(): void
    {
        $user = new UserModel();

        $card = new UserCardModel();

        $card->user = $user;

        $beforeSave = new SaveUserCardBeforeSave($card);

        $updateStripeCustomer = $this->createMock(
            UpdateStripeCustomer::class,
        );

        $updateStripeCustomer->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($user))
            ->willThrowException(new Exception());

        $saveNewCard = $this->createMock(
            SaveNewCard::class,
        );

        $saveNewCard->expects(self::never())
            ->method(self::anything());

        $saveExistingCard = $this->createMock(
            SaveExistingCard::class,
        );

        $saveExistingCard->expects(self::never())
            ->method(self::anything());

        $event = new OnBeforeSaveUserCard(
            $updateStripeCustomer,
            $saveNewCard,
            $saveExistingCard,
        );

        $event->onBeforeSaveUserCard($beforeSave);

        self::assertFalse($beforeSave->isValid);
    }

    public function testSaveNew(): void
    {
        $user = new UserModel();

        $card = new UserCardModel();

        $card->user = $user;

        $beforeSave = new SaveUserCardBeforeSave($card);

        $updateStripeCustomer = $this->createMock(
            UpdateStripeCustomer::class,
        );

        $updateStripeCustomer->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($user));

        $saveNewCard = $this->createMock(
            SaveNewCard::class,
        );

        $saveNewCard->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($card));

        $saveExistingCard = $this->createMock(
            SaveExistingCard::class,
        );

        $saveExistingCard->expects(self::never())
            ->method(self::anything());

        $event = new OnBeforeSaveUserCard(
            $updateStripeCustomer,
            $saveNewCard,
            $saveExistingCard,
        );

        $event->onBeforeSaveUserCard($beforeSave);

        self::assertTrue($beforeSave->isValid);
    }

    public function testSaveExisting(): void
    {
        $user = new UserModel();

        $card = new UserCardModel();

        $card->stripeId = 'fooStripeId';

        $card->user = $user;

        $beforeSave = new SaveUserCardBeforeSave($card);

        $updateStripeCustomer = $this->createMock(
            UpdateStripeCustomer::class,
        );

        $updateStripeCustomer->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($user));

        $saveNewCard = $this->createMock(
            SaveNewCard::class,
        );

        $saveNewCard->expects(self::never())
            ->method(self::anything());

        $saveExistingCard = $this->createMock(
            SaveExistingCard::class,
        );

        $saveExistingCard->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo($card));

        $event = new OnBeforeSaveUserCard(
            $updateStripeCustomer,
            $saveNewCard,
            $saveExistingCard,
        );

        $event->onBeforeSaveUserCard($beforeSave);

        self::assertTrue($beforeSave->isValid);
    }
}
