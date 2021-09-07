<?php

namespace App\Controller\LoadPlan;

use App\Entity\User;
use App\Entity\Project;
use App\Entity\LoadPlan;
use App\Controller\BaseController;
use App\Utils\DateHelper;
use App\Service\LoadPlan\LoadPlanService;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
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

    #[Route('/{id}/update/realization/date', name: 'load_plan.update_realization_quotation_date', options: ['expose' => true], requirements: ['id' => '\d+'])]
    public function updateRealizationDate(Request $request, LoadPlan $loadPlan, TranslatorInterface $translator, EntityManagerInterface $em)
    {
        if ($request->getMethod() == 'POST') {
            $content = $request->toArray();
            $newRealizationDate = \DateTime::createFromFormat('Y-m-d', $content['realizationDate']);
            $loadPlan->setRealizationQuotationDate($newRealizationDate);
            $em->flush();

            return $this->json(['message' => $translator->trans('load_plan.messages.realization_date_updated', [], 'projects')], 200);
        }

        return $this->json(['message' => $translator->trans('load_plan.messages.failed_to_update_realization_date', [], 'projects'), 400]);
    }

    #[Route('/{id}/update/deadline', name: 'load_plan.update_deadline_date', options: ['expose' => true], requirements: ['id' => '\d+'])]
    public function updateDeadlineDate(Request $request, LoadPlan $loadPlan, TranslatorInterface $translator, EntityManagerInterface $em)
    {
        if ($request->getMethod() == 'POST') {
            $content = $request->toArray();
            $newDeadlineDate = \DateTime::createFromFormat('Y-m-d', $content['newDeadlineDate']);
            $loadPlan->setDeadline($newDeadlineDate);
            $em->flush();
            
            return $this->json(['message' => $translator->trans('load_plan.messages.deadline_date_updated_successfull', [], 'projects')], 200);
        }

        return $this->json(['message' => $translator->trans('load_plan.messages.deadline_date_updated_failed', [], 'projects'), 400]);
    }

    #[Route('/{id}/update/field', name: 'load_plan.update_start_date', options: ['expose' => true], requirements: ['id' => '\d+'])]
    public function updateStartDate(Request $request, LoadPlan $loadPlan, TranslatorInterface $translator, EntityManagerInterface $em)
    {
        if ($request->getMethod() == 'POST') {
            $content = $request->toArray();
            $newStartDate = \DateTime::createFromFormat('Y-m-d', $content['startDate']);
            $newEndDate = (\DateTime::createFromFormat('Y-m-d', $content['startDate']))->modify('Next sunday');
            $loadPlan->setStart($newStartDate);
            $loadPlan->setEnd($newEndDate);
            $em->flush();

            return $this->json(['message' => $translator->trans('load_plan.messages.start_date_updated_success', [], 'projects')], 200);
        }

        return $this->json(['message' => $translator->trans('load_plan.messages.start_date_updated_failed', [], 'projects')], 400);
    }

    /**
     * Comptage de charge pour la semaine
     */
    #[Route('/{date}/week/load/metering', name: 'load_plan.week_load_metering', options: ['expose' => true])]
    public function weekLoadMetering(
        Request $request, 
        $date, 
        DateHelper $dateHelper, 
        LoadPlanService $loadPlanService,
        EntityManagerInterface $em,
        SerializerInterface $serializer
    )
    {
        if (!$date) {
            $date = new \DateTime();
        } else {
            $date = \DateTime::createFromFormat('Y-m-d', $date);
        }

        $economistIds = [];
        $economistsWeeklyStudyTime = [];
        $firstWeekStart = $dateHelper->getStartOfWeek($date);
        $firstWeekEnd = $dateHelper->getEndOfWeek($firstWeekStart);
        $nextWeekStart = $dateHelper->getStartOfWeek((clone $firstWeekEnd)->modify('+1 day'));
        $nextWeekEnd = $dateHelper->getEndOfWeek($nextWeekStart);
        
        $res = [
            [
                'start' => $firstWeekStart, 
                'end' => $firstWeekEnd
            ], 
            [
                'start' => $nextWeekStart, 
                'end' => $nextWeekEnd
            ]
        ];
        
        foreach ($res as $date) {
            $economistsWeeklyStudyTime[] = $loadPlanService->getEconomistWeeklyStudyTime($date['start'], $date['end']);
        }
        foreach ($economistsWeeklyStudyTime as $key => $res) {
            foreach ($res as $key => $economistStudyTime) {
                $economistIds[] = $economistStudyTime['economistId'];
            }
        }
        $economists = $em->getRepository(User::class)->getEconomistByIds(array_unique($economistIds));
        $economistMap = [];
        foreach ($economists as $economist) {
            $economistMap[$economist->getId()] = $serializer->normalize($economist, 'json', ['groups' => 'load_plan:economist']);
        }

        foreach ($economistsWeeklyStudyTime as $k => $economistWeekStudyTime) {
            foreach ($economistWeekStudyTime as $j => $economistStudyTime) {
                $economistsWeeklyStudyTime[$k][$j]['economist'] = $economistMap[$economistStudyTime['economistId']];
            }
        }

        return $this->json($economistsWeeklyStudyTime);
    }
}