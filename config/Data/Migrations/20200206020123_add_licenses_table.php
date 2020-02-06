<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

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
                'notes',
                'string',
                ['default' => '']
            )
            ->addColumn(
                'authorized_domains',
                'string',
                ['default' => '']
            )
            ->addColumn(
                'is_disabled',
                'boolean',
                ['default' => 0]
            )
            ->create();
    }
}
