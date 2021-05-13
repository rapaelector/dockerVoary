<?php

namespace App\MessageHandler\User;

use App\Message\User\PasswordResetMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;

final class PasswordResetMessageHandler implements MessageHandlerInterface
{
    /** @var MailerInterface */
    private $mailer;

    /** @var TranslatorInterface */
    private $translatorInterface;

    /** @var string */
    private $email;

    /** @var ResetPasswordToken */
    private $resetToken;

    public function __construct(MailerInterface $mailer, TranslatorInterface $translatorInterface)
    {
        $this->mailer = $mailer;
        $this->translatorInterface = $translatorInterface;
    }

    public function __invoke(PasswordResetMessage $message)
    {
        $this->email = $message->getEmail();
        $this->resetToken = $message->getResetToken();
        $this->sendEmail();
    }

    /**
     * Send password reset email
     */
    public function sendEmail()
    {
        $email = (new TemplatedEmail())
            ->to($this->email)
            ->priority(Email::PRIORITY_HIGH)
            ->subject($this->translatorInterface->trans('reset_password.email.subject', [], 'users'))
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'resetToken' => $this->resetToken,
            ])
            ;

        $this->mailer->send($email);
    }
}
