<?php

namespace App\Service\User;

use App\Entity\User;
use App\Entity\Client;
use App\Entity\Project;
use App\Repository\UserRepository;
use App\Utils\Resolver;

use Symfony\Component\Security\Core\Security;
use Symfony\Component\Asset\Packages;
use Vich\UploaderBundle\Storage\StorageInterface;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    /** @var UserRepository $repository */
    private $repository;

    /** @var EntityManagerInterface $em */
    private $em;

    public function __construct(
        UserRepository $repository, 
        EntityManagerInterface $em, 
        Packages $packages, 
        StorageInterface $storage,
        Security $security
    )
    {
        $this->repository = $repository;
        $this->em = $em;
        $this->packages = $packages;
        $this->storage = $storage;
        $this->security = $security;
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
        $newUserEmail = '_' . (new \DateTime())->getTimestamp() . '_' .$user->getEmail();
        $user->setEmail($newUserEmail);
        $this->em->flush();
        
        return true;
    }

    public function getUserAvatar(?User $user = null): string
    {
        return $this->getUserPhoto($user, 'profileName', 'profileFile', 'user.png', 'images');
    }

    public function getUserPhoto(?User $user = null, $prop, $uploadableField, $defaultValue, $packageName = 'images'): string
    {
        if ($user) {
            if (Resolver::resolve([$user, $prop], null)) {
                return $this->storage->resolveUri($user, $uploadableField, User::class);
            }
        } else if ($user = $this->security->getUser()) {
            if (Resolver::resolve([$user, $prop], null)) {
                return $this->storage->resolveUri($user, $uploadableField, User::class);
            }
        }

        return $this->packages->getUrl($defaultValue, $packageName);
    }
}