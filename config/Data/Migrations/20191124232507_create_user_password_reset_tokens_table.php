<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/** @noinspection AutoloadingIssuesInspection */
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Classes.ClassFileName.NoMatch

class CreateUserPasswordResetTokensTable extends AbstractMigration
{
    public function change() : void
    {
        $this->table('user_password_reset_tokens', [
            'id' => false,
            'primary_key' => ['id'],
        ])
            ->addColumn('id', 'uuid', ['comment' => 'UUID is generated in code'])
            ->addColumn('user_id', 'uuid', ['comment' => "User's id"])
            ->addColumn('created_at', 'datetime', ['timezone' => true])
            ->create();
    }
}
