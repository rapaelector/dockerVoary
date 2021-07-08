<?php

namespace App\Repository;

use App\Entity\ProjectMeta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProjectMeta|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectMeta|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectMeta[]    findAll()
 * @method ProjectMeta[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectMetaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectMeta::class);
    }

    // /**
    //  * @return ProjectMeta[] Returns an array of ProjectMeta objects
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
    public function findOneBySomeField($value): ?ProjectMeta
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
