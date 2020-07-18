<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/** @noinspection AutoloadingIssuesInspection */
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Classes.ClassFileName.NoMatch

class AddOrdersTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('orders', [
            'id' => false,
            'primary_key' => ['id'],
        ])
            ->addColumn(
                'id',
                'uuid',
                ['comment' => 'UUID is generated in code']
            )
            ->addColumn(
                'old_order_number',
                'biginteger',
                ['signed' => false]
            )
            ->addColumn(
                'user_id',
                'uuid',
                [
                    'comment' => "User's id",
                    'null' => true,
                ]
            )
            ->addColumn(
                'stripe_id',
                'string',
                ['default' => '']
            )
            ->addColumn(
                'stripe_amount',
                'float',
                ['signed' => false]
            )
            ->addColumn(
                'stripe_balance_transaction',
                'string',
                ['default' => '']
            )
            ->addColumn(
                'stripe_captured',
                'boolean',
                ['default' => 0]
            )
            ->addColumn(
                'stripe_created',
                'integer',
                ['signed' => false]
            )
            ->addColumn(
                'stripe_currency',
                'string',
                ['default' => '']
            )
            ->addColumn(
                'stripe_paid',
                'boolean',
                ['default' => 0]
            )
            ->addColumn(
                'subtotal',
                'float',
                ['default' => 0]
            )
            ->addColumn(
                'tax',
                'float',
                ['default' => 0]
            )
            ->addColumn(
                'total',
                'float',
                ['default' => 0]
            )
            ->addColumn(
                'name',
                'string',
                ['default' => '']
            )
            ->addColumn(
                'company',
                'string',
                ['default' => '']
            )
            ->addColumn(
                'phone_number',
                'string',
                ['default' => '']
            )
            ->addColumn(
                'country',
                'string',
                ['default' => '']
            )
            ->addColumn(
                'address',
                'string',
                ['default' => '']
            )
            ->addColumn(
                'address_continued',
                'string',
                ['default' => '']
            )
            ->addColumn(
                'city',
                'string',
                ['default' => '']
            )
            ->addColumn(
                'state',
                'string',
                ['default' => '']
            )
            ->addColumn(
                'postal_code',
                'string',
                ['default' => '']
            )
            ->addColumn(
                'date',
                'datetime',
                ['timezone' => true]
            )
            ->create();
    }
}
