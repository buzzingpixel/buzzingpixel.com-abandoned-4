<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/** @noinspection AutoloadingIssuesInspection */
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Classes.ClassFileName.NoMatch

class AddLicensesTable extends AbstractMigration
{
    public function change() : void
    {
        $this->table('licenses', [
            'id' => false,
            'primary_key' => ['id'],
        ])
            ->addColumn(
                'id',
                'uuid',
                ['comment' => 'UUID is generated in code']
            )
            ->addColumn(
                'item_key',
                'string',
                ['default' => '']
            )
            ->addColumn(
                'item_title',
                'string',
                ['default' => '']
            )
            ->addColumn(
                'major_version',
                'string',
                ['default' => '']
            )
            ->addColumn(
                'version',
                'string',
                ['default' => '']
            )
            ->addColumn(
                'last_available_version',
                'string',
                ['default' => '']
            )
            ->addColumn(
                'notes',
                'text',
                ['default' => '']
            )
            ->addColumn(
                'authorized_domains',
                'text',
                ['default' => '']
            )
            ->addColumn(
                'is_disabled',
                'text',
                ['default' => 0]
            )
            ->create();
    }
}
