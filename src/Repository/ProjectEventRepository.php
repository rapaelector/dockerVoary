<?php

namespace App\Repository;

use App\Entity\ProjectEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProjectEvent|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectEvent|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectEvent[]    findAll()
 * @method ProjectEvent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectEvent::class);
    }

    public function getEventsBetweenDate(\DateTime $start = null, \DateTime $end = null)
    {
        $qb = $this->createQueryBuilder('p');

        if ($start && $end) {
            $qb
                ->where('(p.start BETWEEN :start AND :end) OR (p.end BETWEEN :start AND :end) OR (p.start <= :start AND p.end >= :end)')
                ->setParameters([
                    'start' => $start,
                    'end' => $end,
                ])
            ;
        }

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }
    // /**
    //  * @return ProjectEvent[] Returns an array of ProjectEvent objects
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
    public function findOneBySomeField($value): ?ProjectEvent
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
