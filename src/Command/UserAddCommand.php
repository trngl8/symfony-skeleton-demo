<?php

namespace App\Command;

use App\Entity\Profile;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:user:add',
    description: 'Add system user',
)]
class UserAddCommand extends Command
{
    private $userManager;

    private $em;

    public function __construct(EntityManagerInterface $em, UserService $userManager)
    {
        $this->userManager = $userManager;
        $this->em = $em;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp(<<<'HELP'
<info>Add User</info>
Arguments: username, name, email
HELP
            )
            ->addArgument('username', InputArgument::REQUIRED)
            ->addArgument('password', InputArgument::REQUIRED)
            ->addOption('v', null, InputOption::VALUE_NONE, 'Verbose')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $plaintextPassword = $input->getArgument('password');
        $username = $input->getArgument('username');

        $user = $this->userManager->create($username, $plaintextPassword);

        $this->userManager->save($user);

        //TODO create profile in the factory
        $profile = (new Profile())
            ->setName($username)
            ->setEmail($username)
            ->setTimezone(date_default_timezone_get())
            ->setLocale('uk') //TODO get default locale
            ->setActive(true)
        ;

        $this->em->getRepository(Profile::class)->add($profile);
        $this->em->flush();

        $io->success('User successfully generated!');

        return Command::SUCCESS;
    }


}
