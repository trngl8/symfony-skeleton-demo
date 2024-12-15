<?php

namespace App\Command;

use App\Entity\Job;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:cron',
    description: 'Main crontab command'
)]

class CrontabCommand extends Command
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp(<<<'HELP'
<info>Crontab</info>
Crontab uses for running scheduled tasks.
<info>Usage</info>
Type in terminal:
<comment>php bin/console app:cron</comment>
HELP
            )
            ->addOption('v', null, InputOption::VALUE_NONE, 'Verbose')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $table = new Table($output);
        $io->title('Crontab');
        $rows = [];
        foreach ($this->getJobs() as $line) {
            $rows[] = $line;
        }

        $table
            ->setHeaders(['ID', 'Title', 'Crontab'])
            ->setRows($rows)
        ;
        $table->render();

        return Command::SUCCESS;
    }

    private function getJobs(): \Generator
    {
        $jobs = $this->em->getRepository(Job::class)->findAll();
        foreach ($jobs as $job) {
            $line = [
                'id' => $job->getId(),
                'title' => $job->getTitle(),
                'crontab' => $job->getCrontab(),
            ];
            yield $line;
        }
    }
}
