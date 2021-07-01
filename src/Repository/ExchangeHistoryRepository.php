<?php

namespace App\Repository;

use App\Entity\ExchangeHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ExchangeHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExchangeHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExchangeHistory[]    findAll()
 * @method ExchangeHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExchangeHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExchangeHistory::class);
    }

    // /**
    //  * @return ExchangeHistory[] Returns an array of ExchangeHistory objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ExchangeHistory
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
