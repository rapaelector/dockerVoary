<?php

namespace App\Controller\Project;

use App\Controller\BaseController;
use App\Entity\Project;
use App\Entity\Constants\Project as ProjectConstants;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/project')]
class NgProjectHelperController extends BaseController
{
    #[Route('/{id}/scheduler/project', name: '', options: ['expose' => true])]
    public function projectSchedulerProject(Project $project, SerializerInterface $serializer)
    {
        return $this->json($serializer->normalize($project, 'json', ['groups' => 'project:provisional-order-book']));
    }

    #[Route('/market/type', name: 'project.ng.market_type', options: ['expose' => true])]
    public function marketType(TranslatorInterface $translator)
    {
        $marketTypes = array_map(function ($marketType) use ($translator) {
            return ['label' => $translator->trans($marketType, [], 'project'), 'value' => $marketType];
        }, ProjectConstants::TYPE_DE_MARCHE);

        return $this->json($marketTypes);
    }
}