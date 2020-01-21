<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/** @noinspection AutoloadingIssuesInspection */
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Classes.ClassFileName.NoMatch

class CreateCartTable extends AbstractMigration
{
    public function change() : void
    {
        $this->table('cart', [
            'id' => false,
            'primary_key' => ['id'],
        ])
            ->addColumn('id', 'uuid', ['comment' => 'UUID is generated in code'])
            ->addColumn('user_id', 'uuid', ['comment' => "User's id"])
            ->addColumn('total_items', 'smallinteger', ['signed' => false])
            ->addColumn('total_quantity', 'smallinteger', ['signed' => false])
            ->addColumn('last_touched_at', 'datetime', ['timezone' => true])
            ->addColumn('created_at', 'datetime', ['timezone' => true])
            ->create();
    }
}
