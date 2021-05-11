<?php

namespace App\MessageHandler\User;

use App\Repository\UserRepository;
use App\Message\User\UserCreatedMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserBaseMessageHandler
{
     /** @var MailerInterface */
    protected $mailer;

    /** @var UserRepository $userRepository */
    protected $userRepository;

    /** @var TranslatorInterface $translator */
    protected $translator;

    public function __construct(MailerInterface $mailer, UserRepository $userRepository, TranslatorInterface $translator)
    {
        $this->mailer = $mailer;
        $this->userRepository = $userRepository;
        $this->translator = $translator;
    }
}