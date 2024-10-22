<?php

namespace App\Repository;

use App\Entity\SweatShirt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * SweatShirtRepository
 *
 * This repository is responsible for managing SweatShirt entities.
 * It extends ServiceEntityRepository to provide common database operations.
 *
 * @extends ServiceEntityRepository<SweatShirt>
 */
class SweatShirtRepository extends ServiceEntityRepository
{
    /**
     * Constructor for SweatShirtRepository.
     *
     * @param ManagerRegistry $registry The Doctrine registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SweatShirt::class);
    }

    /**
     * Find top sweatshirts.
     *
     * This method retrieves up to 3 sweatshirts marked as top.
     *
     * @return SweatShirt[] An array of SweatShirt objects
     */
    public function showTop(): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.top = 1')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find sweatshirts within a specific price range.
     *
     * This method retrieves sweatshirts with prices between the given minimum and maximum values.
     * If no price range is provided, it returns all sweatshirts.
     *
     * @param array $prices An array containing the minimum and maximum price values
     * @return SweatShirt[] An array of SweatShirt objects
     */
    public function findWithPriceRange(array $prices): array
    {
        if ($prices && count($prices) == 2) {
            $minPrice = min($prices);
            $maxPrice = max($prices);

            return $this->createQueryBuilder('r')
                ->where('r.price >= :minPrice')
                ->andWhere('r.price < :maxPrice')
                ->orderBy('r.price', 'ASC')
                ->setParameter('minPrice', $minPrice)
                ->setParameter('maxPrice', $maxPrice)
                ->getQuery()
                ->getResult();
        }

        return $this->findAll();
    }

    /**
     * Find sweatshirts by a specific field.
     *
     * This is an example method showing how to create a custom query.
     * It's currently commented out and can be uncommented and modified as needed.
     *
     * @param mixed $value The value to search for
     * @return SweatShirt[] Returns an array of SweatShirt objects
     */
    /*
    public function findByExampleField($value): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /**
     * Find a single sweatshirt by a specific field.
     *
     * This is another example method for finding a single SweatShirt entity.
     * It's currently commented out and can be uncommented and modified as needed.
     *
     * @param mixed $value The value to search for
     * @return SweatShirt|null Returns a SweatShirt object or null if not found
     */
    /*
    public function findOneBySomeField($value): ?SweatShirt
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}