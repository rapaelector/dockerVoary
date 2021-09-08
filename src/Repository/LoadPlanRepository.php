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

    public function getEventsBetweenDate(\DateTime $start = null, \DateTime $end = null)
    {
        $qb = $this->createQueryBuilder('l');

        if ($start && $end) {
            $qb
                ->where('(l.start BETWEEN :start AND :end) OR (l.end BETWEEN :start AND :end) OR (l.start <= :start AND l.end >= :end)')
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

    public function getEconomistIds(): array
    {
        $res = $this->createQueryBuilder('l')
            ->select('DISTINCT(economist) economistId')
            ->leftJoin('l.project', 'project')
            ->leftJoin('project.economist', 'economist')
            ->getQuery()
            ->getResult()
        ;

        return array_map(fn($id) => (int) $id, array_column($res, 'economistId'));
    }
    
    public function getProjectIds(): array
    {
        $res = $this->createQueryBuilder('l')
            ->select('DISTINCT(project) projectId')
            ->leftJoin('l.project', 'project')
            ->getQuery()
            ->getResult()
        ;
        
        return array_map(fn($id) => (int) $id, array_column($res, 'projectId'));
    }

    public function getWeeklyStudyTimeCountPerEconomist(\DateTime $start, \DateTime $end)
    {
        return $this->createQueryBuilder('l')
            ->select('SUM(l.estimatedStudyTime) estimatedStudyTime, economist.id economistId')
            ->leftJoin('l.project', 'project')
            ->leftJoin('project.economist', 'economist')
            ->where('l.deadline BETWEEN :start AND :end')
            ->setParameters([
                'start' => $start->format('Y-m-d 00:00:00'),
                'end' => $end->format('Y-m-d 23:59:59'),
            ])
            ->groupBy('economist')
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
