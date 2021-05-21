<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Client;
use App\Form\User\ContactType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

#[Route('/contact')]
class ContactController extends AbstractController
{
    /**
     * @Security("is_granted(constant('\\App\\Security\\Voter\\Attributes::EDIT'), client)")
     */    
    #[Route('/{id}/{client}/edit', name: 'contact.edit')]
    public function edit(Request $request, User $contact, Client $client, EntityManagerInterface $em, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', $translator->trans('messages.contact_edited_success', [], 'contact'));

            return $this->redirectToRoute('client.show', ['id' => $client->getId()]);
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
