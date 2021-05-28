<?php

namespace App\Controller;

use App\Controller\BaseController;
use App\Entity\User;
use App\Entity\Client;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/dashboard')]
class DashboardController extends BaseController
{
    #[Route('/get-box-stats', name: 'dashboard.get.box_stats', options: ['expose' => true])]
    public function getBoxStats(Request $request, EntityManagerInterface $em)
    {
        $userCount = $em->getRepository(User::class)->countUsers();
        $prospectCount = $em->getRepository(Client::class)->countClients();

        return $this->json([
            'usersCount' => $userCount,
            'prospectsCount' => $prospectCount,
            'projectsCount' => 0,
            'offresCount' => 0,
        ]);
    }
}