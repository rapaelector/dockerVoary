<?php

namespace App\Command\User;

use App\Entity\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;

class UserDemoteCommand extends Command
{
    protected static $defaultName = 'app:user:demote';
    protected static $defaultDescription = 'Remove user role';

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
            ->addArgument('email', InputArgument::REQUIRED, 'User email to remove the role')
            ->addArgument('role', InputArgument::REQUIRED, 'Role remove from the user')
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

        $user->removeRole($role);
        $this->em->flush();

        $io->success('Role removed from the user successfull');

        return Command::SUCCESS;
    }
}
