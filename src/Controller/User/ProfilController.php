<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Form\UserType;
use App\Form\User\ProfilType;
use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\ORM\EntityManagerInterface;

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
}