<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/** @noinspection AutoloadingIssuesInspection */
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Classes.ClassFileName.NoMatch

class AddStripeIdToUsers extends AbstractMigration
{
    public function change() : void
    {
        $this->table('users')
            ->addColumn(
                'stripe_id',
                'string',
                ['default' => '']
            )
            ->update();
    }
}
