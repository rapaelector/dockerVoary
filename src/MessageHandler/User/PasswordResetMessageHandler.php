<?php

namespace App\MessageHandler\User;

use App\Message\User\PasswordResetMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

final class PasswordResetMessageHandler implements MessageHandlerInterface
{
    /** @var MailerInterface */
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function __invoke(PasswordResetMessage $message)
    {
        $this->sendEmail();
    }

    /**
     * Send password reset email
     */
    public function sendEmail()
    {
        $email = (new TemplatedEmail())
            // ->from('hello@example.com')
            // ->to('you@example.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->htmlTemplate('email/user/resetting/reset.html.twig');

        $this->mailer->send($email);
    }
}
