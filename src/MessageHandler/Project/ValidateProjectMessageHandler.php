<?php

namespace App\MessageHandler\Project;

use App\Entity\Project;
use App\Message\Project\ValidateProject;
use App\Utils\Resolver;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Email;

class ValidateProjectMessageHandler implements MessageHandlerInterface
{
    /** @var MailerInterface */
    private $mailer;

    /** @var TranslatorInterface */
    private $translator;

    /** @var string */
    private $email;

    public function __construct(MailerInterface $mailer, TranslatorInterface $translatorInterface)
    {
        $this->mailer = $mailer;
        $this->translator = $translatorInterface;
    }

    public function __invoke(ValidateProject $message)
    {
        $this->sendEmail($message->getProject());
    }

    private function sendEmail(Project $project)
    {   
        $sendTo = Resolver::resolve([$project, 'contact', 'email'], null);

        $email = (new TemplatedEmail())
            ->subject($this->translator->trans('email.ask_for_validation.title', [], 'project'))
            ->htmlTemplate('email/project/ask_for_project_validation.html.twig')
            ->context([
                'project' => $project,
            ])
        ;

        if ($sendTo) {
            $email->to($sendTo);
        }

        $this->mailer->send($email);
    }
}