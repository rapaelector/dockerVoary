<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserPromoteCommand extends Command
{
    protected static $defaultName = 'app:user:promote';
    protected static $defaultDescription = 'Add a short description for your command';

    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('email', InputArgument::REQUIRED, 'Email to bind the role')
            ->addArgument('role', InputArgument::REQUIRED, 'Role to bind to the user by his mail')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $role = $input->getArgument('role');

        $user = $this->em->getRepository(User::class)->findOneByEmail($email);
        if (!$user) {
            $io->error('User not found!!');
            
            return 0;
        }

        $user->addRole($role);
        $this->em->flush();

        $io->success('Role ' . $role . ' added to the user ' . $user->getEmail() . ' successfull');

        return Command::SUCCESS;
    }
}
