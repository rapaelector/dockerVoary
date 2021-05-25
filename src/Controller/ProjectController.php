<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Snappy\Pdf;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;

#[Route('/project')]
class ProjectController extends AbstractController
{
    #[Route('/', name: 'project.index')]
    public function index(): Response
    {
        return $this->render('project/pdf/index.html.twig', ['previewMode' => true]);
    }

    #[Route('/pdf', name: 'project.pdf')]
    public function pdf(Pdf $knpSnappyPdf)
    {
        $template = $this->renderView('project/pdf/index.html.twig'); 
        
        return new PdfResponse($knpSnappyPdf->getOutputFromHtml($template));
    }
}
