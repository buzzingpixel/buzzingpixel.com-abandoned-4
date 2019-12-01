<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/** @noinspection AutoloadingIssuesInspection */
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Classes.ClassFileName.NoMatch

class CreateSoftwareVersionsTable extends AbstractMigration
{
    public function change() : void
    {
        $this->table('software_versions', [
            'id' => false,
            'primary_key' => ['id'],
        ])
            ->addColumn('id', 'uuid', ['comment' => 'UUID is generated in code'])
            ->addColumn('software_id', 'uuid')
            ->addColumn('major_version', 'text')
            ->addColumn('version', 'text')
            ->addColumn('download_file', 'text')
            ->addColumn('released_on', 'datetime', ['timezone' => true])
            ->create();
    }
}
