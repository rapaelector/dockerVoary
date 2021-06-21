<?php

namespace App\Repository;

use App\Entity\Relaunch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Relaunch|null find($id, $lockMode = null, $lockVersion = null)
 * @method Relaunch|null findOneBy(array $criteria, array $orderBy = null)
 * @method Relaunch[]    findAll()
 * @method Relaunch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RelaunchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Relaunch::class);
    }

    // /**
    //  * @return Relaunch[] Returns an array of Relaunch objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Relaunch
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
