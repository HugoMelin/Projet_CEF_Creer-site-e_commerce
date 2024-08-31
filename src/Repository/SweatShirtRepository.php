<?php

namespace App\Repository;

use App\Entity\SweatShirt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SweatShirt>
 */
class SweatShirtRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SweatShirt::class);
    }

    /**
     * Summary of showTop
     * @return SweatShirt[]
     */
    public function showTop(): array
    {

        return $this->createQueryBuilder('r')
        ->where('r.top = 1')
        ->setMaxResults(3)
        ->getQuery()
        ->getResult();
    }

//    /**
//     * @return SweatShirt[] Returns an array of SweatShirt objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SweatShirt
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
