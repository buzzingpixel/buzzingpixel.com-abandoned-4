<?php

declare(strict_types=1);

namespace Tests\Cart\Services;

use App\Cart\Models\CartItemModel;
use App\Cart\Models\CartModel;
use App\Cart\Services\DeleteCart;
use App\Persistence\Cart\CartItemRecord;
use App\Persistence\Cart\CartRecord;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class DeleteCartTest extends TestCase
{
    public function testWhenNoItems() : void
    {
        $cart     = new CartModel();
        $cart->id = 'foo-cart-id';

        $statement1 = $this->createMock(PDOStatement::class);

        $statement1->expects(self::once())
            ->method('execute')
            ->with(self::equalTo([':id' => 'foo-cart-id']))
            ->willReturn(true);

        $pdo = $this->createMock(PDO::class);

        $pdo->expects(self::once())
            ->method('prepare')
            ->with(self::equalTo(
                'DELETE FROM ' . (new CartRecord())->getTableName() .
                ' WHERE id = :id'
            ))
            ->willReturn($statement1);

        $service = new DeleteCart($pdo);

        $service($cart);
    }

    public function test() : void
    {
        $cartItem1           = new CartItemModel();
        $cartItem1->id       = 'foo-item-id-1';
        $cartItem1->quantity = 1;
        $cartItem2           = new CartItemModel();
        $cartItem2->id       = 'foo-item-id-2';
        $cartItem2->quantity = 1;

        $cart     = new CartModel();
        $cart->id = 'foo-cart-id';
        $cart->addItem($cartItem1);
        $cart->addItem($cartItem2);

        $statement1 = $this->createMock(PDOStatement::class);

        $statement1->expects(self::once())
            ->method('execute')
            ->with(self::equalTo([':id' => 'foo-cart-id']))
            ->willReturn(true);

        $statement2 = $this->createMock(PDOStatement::class);

        $statement2->expects(self::once())
            ->method('execute')
            ->with(self::equalTo(
                [
                    'foo-item-id-1',
                    'foo-item-id-2',
                ]
            ))
            ->willReturn(true);

        $pdo = $this->createMock(PDO::class);

        $pdo->expects(self::at(0))
            ->method('prepare')
            ->with(self::equalTo(
                'DELETE FROM ' . (new CartRecord())->getTableName() .
                ' WHERE id = :id'
            ))
            ->willReturn($statement1);

        $itemTableName = (new CartItemRecord())->getTableName();

        $pdo->expects(self::at(1))
            ->method('prepare')
            ->with(self::equalTo(
                'DELETE FROM ' . $itemTableName .
                ' WHERE id IN (?,?)'
            ))
            ->willReturn($statement2);

        $service = new DeleteCart($pdo);

        $service($cart);
    }
}
