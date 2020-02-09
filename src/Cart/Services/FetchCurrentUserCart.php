<?php

declare(strict_types=1);

namespace App\Cart\Services;

use App\Cart\Models\CartItemModel;
use App\Cart\Models\CartModel;
use App\Software\Models\SoftwareModel;
use App\Users\UserApi;
use buzzingpixel\cookieapi\interfaces\CookieApiInterface;
use DateTimeImmutable;
use DateTimeZone;
use function assert;
use function Safe\strtotime;

class FetchCurrentUserCart
{
    private CookieApiInterface $cookieApi;
    private FetchCartById $fetchCartById;
    private FetchCartByUserId $fetchCartByUserId;
    private UserApi $userApi;
    private SaveCart $saveCart;
    private DeleteCart $deleteCart;

    public function __construct(
        CookieApiInterface $cookieApi,
        FetchCartById $fetchCartById,
        FetchCartByUserId $fetchCartByUserId,
        UserApi $userApi,
        SaveCart $saveCart,
        DeleteCart $deleteCart
    ) {
        $this->cookieApi         = $cookieApi;
        $this->fetchCartById     = $fetchCartById;
        $this->fetchCartByUserId = $fetchCartByUserId;
        $this->userApi           = $userApi;
        $this->saveCart          = $saveCart;
        $this->deleteCart        = $deleteCart;
    }

    public function __invoke() : CartModel
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $currentDate = new DateTimeImmutable(
            'now',
            new DateTimeZone('UTC')
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        $currentDatePlus20Years = $currentDate->setTimestamp(
            strtotime('+ 20 years')
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        $currentDateMinus1Minute = $currentDate->setTimestamp(
            strtotime('- 1 minute')
        );

        $user = $this->userApi->fetchLoggedInUser();

        $cookie = $this->cookieApi->retrieveCookie('cart_id');

        $cookieCart = $cookie !== null ?
            ($this->fetchCartById)($cookie->value()) :
            null;

        $userCart = $user !== null ?
            ($this->fetchCartByUserId)($user->id) :
            null;

        if ($cookieCart !== null && $userCart !== null) {
            foreach ($cookieCart->getItems() as $cookieItem) {
                $cookieItemSoftware = $cookieItem->getSoftware();
                assert($cookieItemSoftware instanceof SoftwareModel);

                $softwareSlug = $cookieItemSoftware->slug;

                $set = false;

                foreach ($userCart->getItems() as $userItem) {
                    $userItemSoftware = $userItem->getSoftware();
                    assert($userItemSoftware instanceof SoftwareModel);

                    if ($userItemSoftware->slug !== $softwareSlug) {
                        continue;
                    }

                    $userItem->setQuantity(
                        $userItem->getQuantity() + $cookieItem->getQuantity()
                    );

                    $set = true;
                }

                if ($set) {
                    continue;
                }

                $item = new CartItemModel([
                    'software' => $cookieItem->getSoftware(),
                    'quantity' => $cookieItem->getQuantity(),
                ]);

                $userCart->addItem($item);
            }

            ($this->saveCart)($userCart);

            ($this->deleteCart)($cookieCart);

            $cookieCart = null;
        }

        if ($user !== null && $userCart === null) {
            if ($cookieCart !== null) {
                $userCart = $cookieCart;

                $userCart->setUser($user);
            } else {
                $userCart = new CartModel(['user' => $user]);
            }

            ($this->saveCart)($userCart);
        }

        if ($user === null && $cookieCart === null) {
            $cookieCart = new CartModel();

            ($this->saveCart)($cookieCart);

            $this->cookieApi->saveCookie(
                $this->cookieApi->makeCookie(
                    'cart_id',
                    $cookieCart->getId(),
                    $currentDatePlus20Years
                )
            );
        }

        if ($userCart !== null) {
            // The cookie api delete cookie function is broken. Need to fix that
            // $this->cookieApi->deleteCookie($cookie);
            $this->cookieApi->saveCookie(
                $this->cookieApi->makeCookie(
                    'cart_id',
                    '',
                    $currentDateMinus1Minute
                )
            );
        }

        $cart = $userCart ?? $cookieCart;
        assert($cart instanceof CartModel);

        return $cart;
    }
}
