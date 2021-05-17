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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserChangePasswordCommand extends Command
{
    protected static $defaultName = 'app:user:change-password';
    protected static $defaultDescription = 'Change user password';

    /** @var EntityManagerInterface */
    private $em;

    /** @var UserPasswordEncoderInterface */
    private $encoder;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        parent::__construct();
        $this->em = $em;
        $this->encoder = $encoder;
    }

    protected function configure()
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('email', InputArgument::REQUIRED, 'User to change the password')
            ->addArgument('newPassword', InputArgument::REQUIRED, 'New password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $newPassword = $input->getArgument('newPassword');

        $user = $this->em->getRepository(User::class)->findOneByEmail($email);
        if (!$user) {
            $io->error('User not found!!');
            
            return 0;
        }

        $newPasswordEncoded = $this->encoder->encodePassword($user, $newPassword);
        $user->setPassword($newPasswordEncoded);
        $this->em->flush();

        $io->success('User password changed successfull');

        return Command::SUCCESS;
    }
}
