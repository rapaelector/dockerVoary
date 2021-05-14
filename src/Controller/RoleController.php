<?php

/**
 * @Author: Stephan<srandriamahenina@bocasay.com>
 * @Date:   2017-07-28 10:11:13
 * @Last Modified by:   stephan
 * @Last Modified time: 2019-06-20 15:15:33
 */

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

use App\Entity\User;
use App\Security\Role\RoleProvider;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("/role")
 */
class RoleController extends BaseController
{
    /**
     * @Route("/", name="roles.management")
     */
    // public function indexAction(Request $request, UserLogoutManager $userLogoutManager)
    public function indexAction(Request $request, EntityManagerInterface $em, TranslatorInterface $translator)
    {
        $parentRoles = RoleProvider::getParentRoles();
        $roles = RoleProvider::loadRoles();
        $users = $em->getRepository(User::class)->findAll();
        $actions = RoleProvider::getActions();
        $uniqueRoles = [];
        $userId = $request->query->get('userId');

        if ($request->getMethod() == Request::METHOD_POST) {
            $routeParams = [];
            $userId = $request->request->get('appbundle_user_id');
            $userRoles = $request->request->get('appbundle_user_roles');
            if (!$userRoles) {
                $userRoles = [];
            }
            $availableRoles = RoleProvider::getRawRoles();
            $removedRoles = array_diff($availableRoles, $userRoles);
            $user = $em->getRepository(User::class)->find($userId);
            if (!is_null($user)) {
                foreach ($removedRoles as $removedRole) {
                    $user->removeRole($removedRole);
                }
                foreach ($userRoles as $selectedRole) {
                    if (in_array($selectedRole, $uniqueRoles)) {
                        $authorizedUsers = $em->getRepository(User::class)->findUserWithRole($selectedRole);
                        foreach ($authorizedUsers as $authorizedUser) {
                            $authorizedUser->removeRole($selectedRole);
                            // $userLogoutManager->addUserLogout($authorizedUser);
                        }
                    }
                    if (in_array($selectedRole, $parentRoles)) {
                        $user->removeRole($selectedRole); // remove parent role
                    } else {
                        $user->addRole($selectedRole);
                    }
                }
                // add logoutUser
                // $userLogoutManager->addUserLogout($user);

                $message = $translator->trans("role.flash.change_success", [
                    '%name%' => $user->getName(),
                    '%username%' => $user->getUsername(),
                ], 'roles');
                $em->flush();
                $this->addFlash('success', $message);
                $routeParams['userId'] = $userId;
            }


            return $this->redirectToRoute('roles.management', $routeParams);
        }

        return $this->render('role/index.html.twig', [
            'roles' => $roles,
            'users' => $users,
            'actions' => $actions,
            'userId' => $userId,
        ]);
    }
}
