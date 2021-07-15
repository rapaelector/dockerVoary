<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/project/schedule')]
class ProjectScheduleController extends AbstractController
{
    #[Route('/', name: 'project_schedule')]
    public function index(): Response
    {
        return $this->render('project_schedule/index.html.twig');
    }
}
