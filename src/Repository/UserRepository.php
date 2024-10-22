<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * UserRepository
 *
 * This repository is responsible for managing User entities.
 * It extends ServiceEntityRepository to provide common database operations
 * and implements PasswordUpgraderInterface for password upgrade functionality.
 *
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    /**
     * Constructor for UserRepository.
     *
     * @param ManagerRegistry $registry The Doctrine registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     *
     * This method is used by Symfony's security system to upgrade password hashes
     * when needed, for example, when the hashing algorithm changes.
     *
     * @param PasswordAuthenticatedUserInterface $user The user whose password needs to be upgraded
     * @param string $newHashedPassword The new hashed password
     * @throws UnsupportedUserException If the user is not an instance of the User entity
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * Find users by a specific field.
     *
     * This is an example method showing how to create a custom query.
     * It's currently commented out and can be uncommented and modified as needed.
     *
     * @param mixed $value The value to search for
     * @return User[] Returns an array of User objects
     */
    /*
    public function findByExampleField($value): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /**
     * Find a single user by a specific field.
     *
     * This is another example method for finding a single User entity.
     * It's currently commented out and can be uncommented and modified as needed.
     *
     * @param mixed $value The value to search for
     * @return User|null Returns a User object or null if not found
     */
    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
