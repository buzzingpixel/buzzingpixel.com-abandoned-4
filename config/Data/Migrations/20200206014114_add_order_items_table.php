<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/** @noinspection AutoloadingIssuesInspection */
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Classes.ClassFileName.NoMatch

class AddOrderItemsTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('order_items', [
            'id' => false,
            'primary_key' => ['id'],
        ])
            ->addColumn(
                'id',
                'uuid',
                ['comment' => 'UUID is generated in code']
            )
            ->addColumn(
                'order_id',
                'uuid',
            )
            ->addColumn(
                'license_id',
                'uuid',
            )
            ->addColumn(
                'item_key',
                'string',
                ['default' => '']
            )
            ->addColumn(
                'item_title',
                'string',
                ['default' => '']
            )
            ->addColumn(
                'major_version',
                'string',
                ['default' => '']
            )
            ->addColumn(
                'version',
                'string',
                ['default' => '']
            )
            ->addColumn(
                'price',
                'float',
                ['signed' => false]
            )
            ->addColumn(
                'original_price',
                'float',
                ['signed' => false]
            )
            ->addColumn(
                'is_upgrade',
                'boolean',
                ['default' => 0]
            )
            ->addColumn(
                'has_been_upgraded',
                'boolean',
                ['default' => 0]
            )
            ->addColumn(
                'expires',
                'datetime',
                ['timezone' => true]
            )
            ->create();
    }
}
