<?php

namespace App\Repository;

use App\Entity\LoadPlan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LoadPlan|null find($id, $lockMode = null, $lockVersion = null)
 * @method LoadPlan|null findOneBy(array $criteria, array $orderBy = null)
 * @method LoadPlan[]    findAll()
 * @method LoadPlan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LoadPlanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LoadPlan::class);
    }

    public function getProjects()
    {
        return $this->createQueryBuilder('l')
            ->leftJoin('l.project', 'project')
            ->getQuery()
            ->getResult()
        ;
    }
    // /**
    //  * @return LoadPlan[] Returns an array of LoadPlan objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LoadPlan
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
