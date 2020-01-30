<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/** @noinspection AutoloadingIssuesInspection */
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Classes.ClassFileName.NoMatch

class AddNewSoftwareFields extends AbstractMigration
{
    public function change() : void
    {
        $this->table('software')
            ->addColumn(
                'price',
                'float',
                [
                    'after' => 'is_for_sale',
                    'default' => '0.0',
                ]
            )
            ->addColumn(
                'renewal_price',
                'float',
                [
                    'after' => 'price',
                    'default' => '0.0',
                ]
            )
            ->addColumn(
                'is_subscription',
                'boolean',
                [
                    'after' => 'renewal_price',
                    'default' => 0,
                ]
            )
            ->update();

        $this->table('software_versions')
            ->addColumn(
                'upgrade_price',
                'float',
                [
                    'after' => 'download_file',
                    'default' => 0,
                ]
            )
            ->update();
    }
}
