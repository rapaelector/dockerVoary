<?php

namespace App\Controller\Project;

use App\Entity\Constants\Project\ProjectLoseReasons;
use App\Controller\BaseController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/project/helper')]
class ProjectHelperController extends BaseController
{
    #[Route('/get/lose-reasons', name: 'project.ng.project_get_lose_reasons', options: ['expose' => true])]
    public function getProjectLoseReasons(TranslatorInterface $translator)
    {
        $translatedReasons = array_map(function ($val) use ($translator) {
            return [
                'label' => $translator->trans('reason.' . $val, [], 'project'),
                'value' => $val,
            ];
        }, ProjectLoseReasons::PROJECT_LOSE_REASONS);

        return $this->json(['reasons' => $translatedReasons]);
    }
}