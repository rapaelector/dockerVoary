<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Client;
use App\Security\BaseVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class ClientVoter extends Voter
{
    const ROLE_CLIENT = 'ROLE_CLIENT';

    /** @var Security $security */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function supports($attribute, $subject)
    {
        if (!in_array($attribute, static::getSupportedAttributes())) {
            return false;
        }

        if (!$subject instanceof Client) {
            return false;
        }

        return true;
    }

    public function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        $methodName = 'can' . $attribute;
        if (method_exists($this, $methodName)) {
            return call_user_func([$this, $methodName], $subject, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    public function canView(Client $client, User $user)
    {
        return $this->security->isGranted('ROLE_CLIENT_VIEW');
    }

    public function canEdit(Client $client, User $user)
    {
        return $this->security->iSGranted('ROLE_CLIENT_EDIT');
    }

    public function canDelete(Client $client, User $user)
    {
        return $this->security->iSGranted('ROLE_CLIENT_DELETE');
    }

    public static function getSupportedAttributes()
    {
        return [
            Attributes::VIEW,
            Attributes::CREATE,
            Attributes::SHOW,
            Attributes::EDIT,
        ];
    }
}