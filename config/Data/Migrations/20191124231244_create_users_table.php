<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/** @noinspection AutoloadingIssuesInspection */
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Classes.ClassFileName.NoMatch

class CreateUsersTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('users', [
            'id' => false,
            'primary_key' => ['id'],
        ])
            ->addColumn('id', 'uuid', ['comment' => 'UUID is generated in code'])
            ->addColumn('is_admin', 'boolean', ['default' => 0])
            ->addColumn('email_address', 'string')
            ->addColumn('password_hash', 'string')
            ->addColumn('is_active', 'boolean', ['default' => 0])
            ->addColumn('first_name', 'string', ['default' => ''])
            ->addColumn('last_name', 'string', ['default' => ''])
            ->addColumn('display_name', 'string', ['default' => ''])
            ->addColumn('billing_name', 'string', ['default' => ''])
            ->addColumn('billing_company', 'string', ['default' => ''])
            ->addColumn('billing_phone', 'string', ['default' => ''])
            ->addColumn('billing_country', 'string', ['default' => ''])
            ->addColumn('billing_address', 'string', ['default' => ''])
            ->addColumn('billing_city', 'string', ['default' => ''])
            ->addColumn('billing_postal_code', 'string', ['default' => ''])
            ->addColumn('created_at', 'datetime', ['timezone' => true])
            ->create();
    }
}
