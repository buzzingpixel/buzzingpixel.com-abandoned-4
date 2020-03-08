<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/** @noinspection AutoloadingIssuesInspection */
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Classes.ClassFileName.NoMatch

class MoveExpiresColumnToLicenseRecord extends AbstractMigration
{
    public function change() : void
    {
        $this->table('licenses')
            ->addColumn(
                'expires',
                'datetime',
                [
                    'after' => 'is_disabled',
                    'timezone' => true,
                ]
            )
            ->update();

        $this->table('order_items')
            ->removeColumn('expires')
            ->update();
    }
}
