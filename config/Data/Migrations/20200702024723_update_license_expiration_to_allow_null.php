<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/** @noinspection AutoloadingIssuesInspection */
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Classes.ClassFileName.NoMatch

class UpdateLicenseExpirationToAllowNull extends AbstractMigration
{
    public function change(): void
    {
        $this->table('licenses')
            ->removeColumn('expires')
            ->update();

        $this->table('licenses')
            ->addColumn(
                'expires',
                'datetime',
                [
                    'after' => 'is_disabled',
                    'timezone' => true,
                    'null' => true,
                ]
            )
            ->update();
    }
}
