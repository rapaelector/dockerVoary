<?php

namespace App\MessageHandler\User;

use App\Repository\UserRepository;
use App\Message\User\UserCreatedMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Contracts\Translation\TranslatorInterface;

final class UserCreatedMessageHandler implements MessageHandlerInterface
{
    /** @var MailerInterface */
    private $mailer;

    /** @var UserRepository $userRepository */
    private $userRepository;

    /** @var TranslatorInterface $translator */
    private $translator;

    public function __construct(MailerInterface $mailer, UserRepository $userRepository, TranslatorInterface $translator)
    {
        $this->mailer = $mailer;
        $this->userRepository = $userRepository;
        $this->translator = $translator;
    }

    public function __invoke(UserCreatedMessage $message)
    {
        $this->sendEmail($message);
    }

    public function sendEmail(UserCreatedMessage $message)
    {
        $userId = $message->getUserId();
        $userPlainPassword = $message->getPlainPassword();
        $user = $this->userRepository->find($userId);

        $email = (new TemplatedEmail())
            // ->from('hello@example.com')
            // ->to('you@example.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject($this->translator->trans('email.user.creation.registration_completed_correctly', [], 'email'))
            ->text($this->translator->trans('email.user.creation.registration_completed_correctly', [], 'email'))
            ->htmlTemplate('email/user/creating/created.html.twig')
            ->context([
                'user' => $user,
                'userPlainPassword' => $userPlainPassword,
            ]);

        $this->mailer->send($email);
    }
}
