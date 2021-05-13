<?php

namespace App\MessageHandler\User;

use App\Message\User\UserCreatedMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class UserCreatedMessageHandler extends UserBaseMessageHandler implements MessageHandlerInterface
{
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
