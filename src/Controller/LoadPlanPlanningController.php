<?php

namespace App\Controller;

use App\Controller\BaseController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/load/plan/planning')]
class LoadPlanPlanningController extends BaseController
{
    #[Route('/', name: 'load_plan_planning.index')]
    public function index(Request $request)
    {
        return $this->render('load_plan_planning/index.html.twig');
    }
}