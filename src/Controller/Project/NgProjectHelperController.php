<?php

namespace App\Controller\Project;

use App\Controller\BaseController;
use App\Entity\Project;
use App\Form\ProjectType;
use App\Form\ProjectOrderBookType;
use App\Entity\Constants\Project as ProjectConstants;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\Form\FormService;

#[Route('/project')]
class NgProjectHelperController extends BaseController
{
    #[Route('/{id}/scheduler/project', name: 'project.ng.project', options: ['expose' => true])]
    public function projectSchedulerProject(Project $project, SerializerInterface $serializer)
    {
        return $this->json($serializer->normalize($project, 'json', ['groups' => 'project:provisional-order-book']));
    }

    #[Route('/ng/config', name: 'project.ng.project_config', options: ['expose' => true])]
    public function config(TranslatorInterface $translator)
    {
        $marketTypes = array_map(function ($marketType) use ($translator) {
            return ['label' => $translator->trans($marketType, [], 'project'), 'value' => $marketType];
        }, ProjectConstants::TYPE_DE_MARCHE);

        $projectTypes = array_map(function ($type) use ($translator) {
            return ['label' => $translator->trans('project_type.'.$type, [], 'projects'), 'value' => $type];
        }, ProjectConstants::TYPES);

        return $this->json([
            'marketTypes' => $marketTypes,
            'projectTypes' => $projectTypes,
        ]);
    }

    #[Route('/{id}/update/project', name: 'project.ng.update_project', options: ['expose' => true])]
    public function updateProject(
        Request $request, 
        Project $project, 
        EntityManagerInterface $em, 
        TranslatorInterface $translator,
        FormService $formService
    )
    {
        $form = $this->createForm(ProjectOrderBookType::class, $project, [
            'csrf_protection' => false,
            'allow_extra_fields' => true,
        ]);

        if ($request->getMethod() == 'POST') {
            $form->submit(json_decode($request->getContent(), true));

            if ($form->isSubmitted() && $form->isValid()) {
                $em->flush();
                
                return $this->json(['message' => $translator->trans('messages.save_order_book_successfull', [], 'projects')], 200);
            }

            return $this->json([
                'errors' => $formService->getErrorsFromForm($form),
                'message' => $translator->trans('messages.save_order_book_failed', [], 'projects'),
            ], 400);
        }

        return $this->json(['message' => $translator->trans('messages.save_order_book_failed', [], 'projects')], 400);
    }

    #[Route('/create/project', name: 'project.ng.create_project', options: ['expose' => true])]
    public function createProject(Request $request, Project $project, EntityManagerInterface $em, TranslatorInterface $translator)
    {
        return $this->json(['message' => 'Créate project by ajax work in progress'], 200);
    }
}