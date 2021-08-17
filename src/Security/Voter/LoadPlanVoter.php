<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\LoadPlan;
use App\Security\BaseVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class LoadPlanVoter extends Voter
{
    const ROLE_LOAD_PLAN = 'ROLE_LOAD_PLAN';

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

        if (!$subject instanceof LoadPlan) {
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

    public function canEdit(LoadPlan $loadPlan, User $user)
    {
        return $this->security->iSGranted('ROLE_LOAD_PLAN_EDIT');
    }

    public function canDelete(LoadPlan $loadPlan, User $user)
    {
        return $this->security->iSGranted('ROLE_LOAD_PLAN_DELETE');
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