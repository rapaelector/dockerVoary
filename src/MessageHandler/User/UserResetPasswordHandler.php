<?php

namespace App\MessageHandler\User;

use App\Message\User\UserResetPasswordMessage;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class UserResetPasswordHandler extends UserBaseMessageHandler implements MessageHandlerInterface
{
    public function __invoke(UserResetPasswordMessage $message)
    {
        $this->sendEmail($message);
    }

    public function sendEmail(UserResetPasswordMessage $message)
    {
        $userId = $message->getUserId();
        $userPlainPassword = $message->getPlainPassword();
        $user = $this->userRepository->find($userId);

        $email = (new TemplatedEmail())
            ->subject($this->translator->trans('email.user.reset_password.reset_password_completed_correctly', [], 'email'))
            ->text($this->translator->trans('email.user.creation.reset_password_completed_correctly', [], 'email'))
            ->htmlTemplate('email/user/reset_password.html.twig')
            ->context([
                'user' => $user,
                'userPlainPassword' => $userPlainPassword,
            ]);

        $this->mailer->send($email);
    }
}
