<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:data',
    description: 'Create application data'
)]

class DataCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setHelp(<<<'HELP'
<info>First</info>
An command info
<info>Second Law</info>
HELP
            )
            ->addOption('v', null, InputOption::VALUE_NONE, 'Verbose')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $db = new \SQLite3(__DIR__ . '/../../var/default.db', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
        $db->enableExceptions(true);

        #$db->query('DROP TABLE IF EXISTS app_meetups'); //TODO: move drop to the configuration option
        $db->query('CREATE TABLE IF NOT EXISTS app_meetups(
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            title VARCHAR(255) NOT NULL,
            planned_at DATETIME NOT NULL,
            timezone VARCHAR(64) NOT NULL,
            created_at DATETIME NOT NULL
        )');

        $db->query('DROP TABLE IF EXISTS app_subscribes');
        $db->query('CREATE TABLE IF NOT EXISTS app_subscribes(
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            type VARCHAR(255) NOT NULL,
            target INTEGER NOT NULL,
            email VARCHAR(255) NOT NULL,
            name VARCHAR(255) NOT NULL
        )');

        // TODO: get data from csv fixtures
        $db->exec('BEGIN');
        $db->query(sprintf('
            INSERT INTO app_meetups(title, planned_at, timezone, created_at) 
            VALUES ("%s", "%s", "%s", "%s")',
                'Meetup 1',
                '2024-01-01 00:00:00',
                'Europe/Kyiv',
                '2023-01-01 00:00:00'
        ));
        $db->exec('COMMIT');

        $db->close();

        $io->writeln('Data created');

        return 1;
    }
}
