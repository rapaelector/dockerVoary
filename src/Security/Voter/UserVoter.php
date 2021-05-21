<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class UserVoter extends Voter
{
    const ROLE_USER = 'ROLE_USER';

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

        if (!$subject instanceof User) {
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

    /**
     * Check if user have role user view
     */
    public function canView(User $user)
    {
        return $this->security->isGranted('ROLE_USER_VIEW');
    }
    
    /**
     * Check if user have role user view
     */
    public function canEdit(User $user)
    {
        return $this->security->isGranted('ROLE_USER_EDIT');
    }

    /**
     * Check if user have role user view
     * The current user cant delete him self
     */
    public function canDelete(User $deletedUser, User $user)
    {
        return $this->security->isGranted('ROLE_USER_DELETE') && ($deletedUser->getId() != $this->security->getUser()->getId());
    }

    public static function getSupportedAttributes()
    {
        return [
            Attributes::VIEW,
            Attributes::CREATE,
            Attributes::SHOW,
            Attributes::EDIT,
            Attributes::DELETE,
        ];
    }
}