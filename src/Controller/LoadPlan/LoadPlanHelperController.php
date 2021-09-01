<?php

namespace App\Controller\LoadPlan;

use App\Entity\User;
use App\Entity\Project;
use App\Entity\LoadPlan;
use App\Controller\BaseController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/load/plan/helper')]
class LoadPlanHelperController extends BaseController
{
    #[Route('/{id}/change/project/economist', name: 'load_plan.change_project_economist', options: ['expose' => true], requirements: ['id' => '\d+'])]
    public function changeProjectEconomist(Request $request, LoadPlan $loadPlan, EntityManagerInterface $em, TranslatorInterface $translator)
    {
        if ($request->getMethod() == 'POST') {
            $loadPlanProject = $loadPlan->getProject();
            $content = $request->toArray();
            $userId  = $content['id'];

            $economist = $em->getRepository(User::class)->find($userId);
            $loadPlanProject->setEconomist($economist);
            $em->flush();

            return $this->json(['message' => $translator->trans('load_plan.messages.project_economist_changed_successfull', [], 'projects')], 200);
        }

        return $this->json(['message' => $translator->trans('load_plan.messages.project_economist_changed_failed', [], 'projects')], 400);
    }
}