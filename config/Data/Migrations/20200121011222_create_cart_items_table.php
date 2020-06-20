<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/** @noinspection AutoloadingIssuesInspection */
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Classes.ClassFileName.NoMatch

class CreateCartItemsTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('cart_items', [
            'id' => false,
            'primary_key' => ['id'],
        ])
            ->addColumn('id', 'uuid', ['comment' => 'UUID is generated in code'])
            ->addColumn('cart_id', 'uuid', ['comment' => "Cart's id"])
            ->addColumn('item_slug', 'string')
            ->addColumn('quantity', 'smallinteger', ['signed' => false])
            ->create();
    }
}
