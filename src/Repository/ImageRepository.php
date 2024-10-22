<?php

namespace App\Repository;

use App\Entity\Image;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * ImageRepository
 *
 * This repository is responsible for managing Image entities.
 * It extends ServiceEntityRepository to provide common database operations.
 *
 * @extends ServiceEntityRepository<Image>
 */
class ImageRepository extends ServiceEntityRepository
{
    /**
     * Constructor for ImageRepository.
     *
     * @param ManagerRegistry $registry The Doctrine registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Image::class);
    }

    /**
     * Find images by a specific field.
     *
     * This is an example method showing how to create a custom query.
     * It's currently commented out and can be uncommented and modified as needed.
     *
     * @param mixed $value The value to search for
     * @return Image[] Returns an array of Image objects
     */
    /*
    public function findByExampleField($value): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /**
     * Find a single image by a specific field.
     *
     * This is another example method for finding a single Image entity.
     * It's currently commented out and can be uncommented and modified as needed.
     *
     * @param mixed $value The value to search for
     * @return Image|null Returns an Image object or null if not found
     */
    /*
    public function findOneBySomeField($value): ?Image
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}