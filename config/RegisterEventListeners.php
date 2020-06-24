<?php

declare(strict_types=1);

namespace Config;

use App\Stripe\EventListeners\OnAfterDeleteUserCard;
use App\Stripe\EventListeners\OnBeforeSaveUser;
use App\Stripe\EventListeners\OnBeforeSaveUserCard;
use App\Users\EventListeners\UserCardBeforeSaveSetDefault;
use Crell\Tukio\OrderedListenerProvider;

class RegisterEventListeners
{
    private OrderedListenerProvider $provider;

    public function __construct(OrderedListenerProvider $provider)
    {
        $this->provider = $provider;
    }

    public function __invoke(): void
    {
        // Method names in subscriber classes must start with `on`. The event
        // will be derived from reflection to see what event it's subscribing to
        // $this->provider->addSubscriber(Test::class, Test::class);
        // public function onBeforeValidate(SaveUserBeforeValidate $beforeValidate) : void
        // {
        //     dd($beforeValidate);
        // }

        $this->provider->addSubscriber(
            OnBeforeSaveUser::class,
            OnBeforeSaveUser::class,
        );

        $this->provider->addSubscriber(
            OnBeforeSaveUserCard::class,
            OnBeforeSaveUserCard::class,
        );

        $this->provider->addSubscriber(
            OnAfterDeleteUserCard::class,
            OnAfterDeleteUserCard::class,
        );

        $this->provider->addSubscriber(
            UserCardBeforeSaveSetDefault::class,
            UserCardBeforeSaveSetDefault::class,
        );
    }
}
