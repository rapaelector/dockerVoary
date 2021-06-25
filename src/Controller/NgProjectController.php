<?php

namespace App\Controller;

use App\Controller\BaseController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/project/ng')]
class NgProjectController extends BaseController
{
    #[Route('/', name: 'project.ng.index')]
    public function index()
    {
        return $this->render('project/ng/index.html.twig');
    }
}