<?php

namespace App\MessageHandler\User;

use App\Message\User\PasswordResetMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;
use Symfony\Component\Mime\Address;

final class PasswordResetMessageHandler implements MessageHandlerInterface
{
    /** @var MailerInterface */
    private $mailer;

    /** @var string */
    private $email;

    /** @var ResetPasswordToken */
    private $resetToken;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
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
            ->from(new Address('rapaelec@gmail.com', 'patrick'))
            ->to($this->email)
            ->priority(Email::PRIORITY_HIGH)
            ->subject('Your password reset request')
            // ->text('Sending emails is fun again!')
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'resetToken' => $this->resetToken,
            ])
            ;

        $this->mailer->send($email);
    }
}
