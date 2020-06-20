<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/** @noinspection AutoloadingIssuesInspection */
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Classes.ClassFileName.NoMatch

class AddUserIdToLicenseRecord extends AbstractMigration
{
    public function change(): void
    {
        $this->table('licenses')
            ->addColumn(
                'owner_user_id',
                'uuid',
                [
                    'after' => 'id',
                    'comment' => "User's id",
                    'null' => true,
                ]
            )
            ->update();
    }
}
