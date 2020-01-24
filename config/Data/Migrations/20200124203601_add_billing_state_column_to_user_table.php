<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/** @noinspection AutoloadingIssuesInspection */
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Classes.ClassFileName.NoMatch

class AddBillingStateColumnToUserTable extends AbstractMigration
{
    public function change() : void
    {
        $this->table('users')
            ->addColumn(
                'billing_state_abbr',
                'string',
                [
                    'after' => 'billing_postal_code',
                    'default' => '',
                ]
            )
            ->update();
    }
}
