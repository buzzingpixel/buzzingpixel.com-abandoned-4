<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/** @noinspection AutoloadingIssuesInspection */
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Classes.ClassFileName.NoMatch

class CreateSubscriptionsTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('subscriptions', [
            'id' => false,
            'primary_key' => ['id'],
        ])
            ->addColumn('id', 'uuid')
            ->addColumn('user_id', 'uuid')
            ->addColumn('license_id', 'uuid')
            ->addColumn(
                'order_ids',
                'json',
                ['null' => true],
            )
            ->addColumn(
                'auto_renew',
                'boolean',
                ['default' => 1]
            )
            ->addColumn(
                'card_id',
                'uuid',
                ['null' => true],
            )
            ->create();
    }
}
