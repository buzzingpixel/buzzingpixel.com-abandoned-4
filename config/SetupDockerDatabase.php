<?php

declare(strict_types=1);

namespace Config;

use PDO;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function getenv;

class SetupDockerDatabase extends Command
{
    /** @var string */
    protected static $defaultName = 'app-setup:setup-docker-database';

    public function execute(InputInterface $input, OutputInterface $output) : void
    {
        $pdo = new PDO(
            'pgsql:host=db;port=5432',
            'postgres',
            'postgres',
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );

        $check = $pdo->query(
            "SELECT 1 from pg_database WHERE datname='buzzingpixel'"
        )->fetch();

        if ($check !== false) {
            $output->writeln(
                '<fg=green>buzzingpixel database is already set up</>'
            );

            return;
        }

        $go = true;

        if (! getenv('DB_USER')) {
            $go = false;

            $output->writeln(
                '<fg=red>DB_USER environment variable is not set</>'
            );
        }

        if (! getenv('DB_PASSWORD')) {
            $go = false;

            $output->writeln(
                '<fg=red>DB_PASSWORD environment variable is not set</>'
            );
        }

        if (! $go) {
            return;
        }

        $output->writeln(
            '<fg=yellow>Creating buzzingpixel database</>'
        );

        $pdo->exec('CREATE DATABASE buzzingpixel');

        $pdo->exec(
            'CREATE USER ' .
            (string) getenv('DB_USER') .
            " WITH ENCRYPTED PASSWORD '" .
            (string) getenv('DB_PASSWORD') .
            "';"
        );

        $pdo->exec(
            'GRANT ALL PRIVILEGES ON DATABASE buzzingpixel TO ' .
            (string) getenv('DB_USER')
        );

        $output->writeln('<fg=green>buzzingpixel database was created</>');
    }
}
