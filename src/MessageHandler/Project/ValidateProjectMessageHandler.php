<?php

namespace App\MessageHandler\Project;

use App\Entity\Project;
use App\Utils\Resolver;
use App\Entity\User;
use App\Entity\Constants\Status;
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
        $actions = $project->getActions();
        $validationRequester = null;

        $mail = (new TemplatedEmail())
            ->subject($this->translator->trans('email.project_validation.title', [], 'project'))
            ->htmlTemplate('email/project/validate_project.html.twig')
            ->context([
                'project' => $project,
            ])
        ;
        foreach ($actions as $action) {
            if ($action->getValue() == Status::STATUS_SUBMITTED) {
                $validationRequester = \App\Utils\EmailUtils::toAddress($action->getCreatedBy());
            }
        }

        $users = $this->em->getRepository(User::class)->getUserWithProjectValidateRole();

        $addresses = \App\Utils\EmailUtils::toAddresses($users);
        if ($validationRequester) {
            $mail->to($validationRequester);
        }
        if (count($addresses) > 0) {
            foreach ($addresses as $address) {
                $mail->addCc($address);
            }
        }

        $this->mailer->send($mail);
    }   
}