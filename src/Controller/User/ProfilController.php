<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Form\UserType;
use App\Form\User\ProfilType;
use App\Form\Model\ChangePassword;
use App\Form\User\ChangePasswordType;
use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

#[Route('/profile')]
class ProfilController extends BaseController
{
    #[Route('/', name: 'profile.view', methods: ['GET'])]
    public function viewProfil()
    {
        return $this->render('user/show_profil.html.twig', ['user' => $this->getUser()]);
    }

    #[Route('/edit', name: 'profile.edit', methods: ['GET', 'POST'])]
    public function editProfil(Request $request, EntityManagerInterface $em, TranslatorInterface $translator)
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfilType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', $translator->trans('messages.profile_edit_success', [], 'users'));

            return $this->redirectToRoute('profile.view');
        }

        return $this->render('user/edit_profil.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    #[Route('/change-password', name: 'profile.change_password', methods: ['GET', 'POST'])]
    public function changePassword(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $em, TranslatorInterface $translator)
    {
        $user = $this->getUser();
        $changePassword = new ChangePassword();
        $form = $this->createForm(ChangePasswordType::class, $changePassword);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $encodedPassword = $encoder->encodePassword($user, $changePassword->newPassword);
            $user->setPassword($encodedPassword);
            $em->flush();
            $this->addFlash('success', $translator->trans('messages.password_changed_success', [], 'users'));
            
            return  $this->redirectToRoute('profile.view');
        }

        return $this->render('user/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}