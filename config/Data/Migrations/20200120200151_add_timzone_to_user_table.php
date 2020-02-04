<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/** @noinspection AutoloadingIssuesInspection */
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Classes.ClassFileName.NoMatch

class AddTimzoneToUserTable extends AbstractMigration
{
    public function change() : void
    {
        $this->table('users')
            ->addColumn('timezone', 'string', [
                'after' => 'is_active',
                'default' => 'US/Central',
            ])
            ->update();
    }
}
