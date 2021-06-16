<?php

namespace App\Repository\Client;

use App\Entity\Client\ProjectDescription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProjectDescription|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectDescription|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectDescription[]    findAll()
 * @method ProjectDescription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectDescriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectDescription::class);
    }

    // /**
    //  * @return ProjectDescription[] Returns an array of ProjectDescription objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProjectDescription
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
