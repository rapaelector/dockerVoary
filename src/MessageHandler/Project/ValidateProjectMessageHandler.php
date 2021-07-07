<?php

namespace App\MessageHandler\Project;

use App\Entity\Project;
use App\Utils\Resolver;
use App\Entity\User;
use App\Message\Project\ValidateProject;

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

    /** @var EntityManagerInterface */
    private $em;

    public function __construct(
        MailerInterface $mailer, 
        TranslatorInterface $translatorInterface,
        EntityManagerInterface $em

    )
    {
        $this->em = $em;
        $this->mailer = $mailer;
        $this->translator = $translatorInterface;
    }

    public function __invoke(ValidateProject $message)
    {
        $this->sendEmail($message->getProject());
    }

    private function sendEmail(Project $project)
    {
        $mail = (new TemplatedEmail())
            ->subject($this->translator->trans('email.project_validation.title', [], 'project'))
            ->htmlTemplate('email/project/validate_project.html.twig')
            ->context([
                'project' => $project,
            ])
        ;
        
        $users = $this->em->getRepository(User::class)->findAll();
        foreach ($users as $user) {
            if (in_array('', $user->getRoles())) {
                $email->to($user->getEmail());
                $this->mailer->send($mail);
            }
        }
    }   
}