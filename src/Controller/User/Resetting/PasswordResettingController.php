<?php

namespace App\Controller\User\Resetting;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PasswordResettingController extends AbstractController
{
    #[Route('/resetting/request', name: 'user.resetting.request')]
    public function resettingRequest(Request $request, \Symfony\Component\Messenger\MessageBusInterface $bus): Response
    {
        $bus->dispatch(new \App\Message\User\PasswordResetMessage);
        if(true) return 'est';
        return new Response('request sent');
    }
}
