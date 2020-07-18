<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/** @noinspection AutoloadingIssuesInspection */
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Classes.ClassFileName.NoMatch

class CreateSoftwareTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('software', [
            'id' => false,
            'primary_key' => ['id'],
        ])
            ->addColumn('id', 'uuid', ['comment' => 'UUID is generated in code'])
            ->addColumn('slug', 'text')
            ->addColumn('name', 'text')
            ->addColumn('is_for_sale', 'boolean', [
                'default' => 0,
                'comment' => 'Whether the software is for sale',
            ])
            ->create();
    }
}
