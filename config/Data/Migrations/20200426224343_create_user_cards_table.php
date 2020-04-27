<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/** @noinspection AutoloadingIssuesInspection */
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Classes.ClassFileName.NoMatch

class CreateUserCardsTable extends AbstractMigration
{
    public function change() : void
    {
        $this->table('user_cards', [
            'id' => false,
            'primary_key' => ['id'],
        ])
            ->addColumn('id', 'uuid')
            ->addColumn('user_id', 'uuid')
            ->addColumn('stripe_id', 'string')
            ->addColumn('nickname', 'string')
            ->addColumn('last_four', 'string')
            ->addColumn('provider', 'string')
            ->addColumn(
                'is_default',
                'boolean',
                ['default' => 0]
            )
            ->addColumn(
                'expiration',
                'datetime',
                ['timezone' => true]
            )
            ->create();
    }
}
