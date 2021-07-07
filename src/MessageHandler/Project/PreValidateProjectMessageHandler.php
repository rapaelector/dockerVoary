<?php

namespace App\MessageHandler\Project;

use App\Entity\Project;
use App\Entity\User;
use App\Message\Project\PreValidateProject;
use App\Entity\Action;
use App\Utils\Resolver;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Email;

class PreValidateProjectMessageHandler implements MessageHandlerInterface
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
        $this->mailer = $mailer;
        $this->em = $em;
        $this->translator = $translatorInterface;
    }

    public function __invoke(PreValidateProject $message)
    {
        $this->sendEmail($message->getProject(), $message->getAction());
    }

    private function sendEmail(Project $project, Action $action)
    {   
        $mailCcRecipients = [];

        $email = (new TemplatedEmail())
            ->subject($this->translator->trans('email.ask_for_validation.title', [], 'project'))
            ->htmlTemplate('email/project/ask_for_project_validation.html.twig')
            ->context([
                'project' => $project,
            ])
        ;
        
        $users = $this->em->getRepository(User::class)
            ->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%ROLE_PROJECT_VALIDATE%')
            ->getQuery()
            ->getResult()
        ;

        $addresses = \App\Utils\EmailUtils::toAddresses($users);
        if (count($addresses) > 0) {
            $email->to(array_shift($addresses));
            foreach ($addresses as $address) {
                $email->addCc($address);
            }
        }

        $this->mailer->send($email);
    }
}