<?php

namespace App\MessageHandler\Project;

use App\Message\Project\Reminder;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ReminderMessageHandler implements MessageHandlerInterface
{
    /** @var MailerInterface */
    private $mailer;

    /** @var TranslatorInterface */
    private $translatorInterface;

    /** @var string */
    private $email;
    /** @var string */
    private $projectSiteCode;

    public function __construct(MailerInterface $mailer, TranslatorInterface $translatorInterface)
    {
        $this->mailer = $mailer;
        $this->translatorInterface = $translatorInterface;
    }

    public function __invoke(Reminder $message)
    {
        $this->email = $message->getEmail();
        $this->projectSiteCode = $message->getProjectSiteCode();
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
            ->subject($this->translatorInterface->trans('reminder.email.subject', [], 'projects'))
            ->htmlTemplate('email/project/reminder.html.twig')
            ->context([
                'projectSiteCode' => $this->projectSiteCode,
            ])
        ;

        $this->mailer->send($email);
    }
}
