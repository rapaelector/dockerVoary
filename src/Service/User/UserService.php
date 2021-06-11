<?php

namespace App\Service\User;

use App\Entity\User;
use App\Entity\Client;
use App\Entity\Project;
use App\Repository\UserRepository;

use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    /** @var UserRepository $repository */
    private $repository;

    /** @var EntityManagerInterface $em */
    private $em;

    public function __construct(UserRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    public function prepareUserRemovable(User $user)
    {
        $projectRepository = $this->em->getRepository(Project::class);

        if (
            $projectRepository->findByRecordAssistant($user) ||
            $projectRepository->findByContact($user) ||
            $projectRepository->findByOcbsDriver($user) ||
            $projectRepository->findByTceDriver($user)
        ) {
            return false;
        }
        $newUserEmail = '_' .rand(0, 9999). '_' .$user->getEmail();
        if ($this->repository->findByEmail($newUserEmail)) {
            $newUserEmail = '_' .rand(0, 9999). '_' .$user->getEmail();
        }
        $user->setEmail($newUserEmail);
        $this->em->flush();
        
        return true;
    }
}