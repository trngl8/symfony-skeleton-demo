<?php

namespace App\Command;

use App\Entity\User;
use App\Service\UserService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:user:list',
    description: 'List system users',
)]
class UserListCommand extends Command
{
    private $userManager;

    public function __construct(UserService $userManager)
    {
        $this->userManager = $userManager;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp(<<<'HELP'
<info>List Users</info>
Arguments: none
HELP
            )
            ->addOption('v', null, InputOption::VALUE_NONE, 'Verbose')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $users = array_map(function ($item) {
            /** @var User $item */
            $roles = implode(', ', $item->getRoles());
            return [$item->getId(), $item->getUsername(), $item->isVerified(), $roles];
        }, $this->userManager->findBy([]));

        $table = new Table($output);
        $table
            ->setHeaders(['ID', 'username', 'verified', 'roles'])
            ->setRows($users)
        ;
        $table->render();

        $io->success('OK');

        return Command::SUCCESS;
    }


}
