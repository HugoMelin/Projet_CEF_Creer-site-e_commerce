<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User entity represents a user in the application.
 * 
 * This entity implements UserInterface and PasswordAuthenticatedUserInterface
 * for Symfony's security system.
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'Un utilisateur utilise déjà cette adresse email.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @var int|null The unique identifier for the user
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var string|null The email address of the user
     */
    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * @var string|null The delivery address of the user
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $delivery_adress = null;

    /**
     * @var string|null The name of the user
     */
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var bool Whether the user's email is verified
     */
    #[ORM\Column]
    private bool $isVerified = false;

    /**
     * Get the ID of the user.
     *
     * @return int|null The user ID
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the email of the user.
     *
     * @return string|null The user email
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set the email of the user.
     *
     * @param string $email The new user email
     * @return static
     */
    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     * @return string The user identifier
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * Get the roles of the user.
     *
     * @see UserInterface
     * @return list<string> The user roles
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    /**
     * Set the roles of the user.
     *
     * @param list<string> $roles The new user roles
     * @return static
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * Get the hashed password of the user.
     *
     * @see PasswordAuthenticatedUserInterface
     * @return string The hashed password
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Set the hashed password of the user.
     *
     * @param string $password The new hashed password
     * @return static
     */
    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Erase the user's credentials.
     *
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * Get the delivery address of the user.
     *
     * @return string|null The delivery address
     */
    public function getDeliveryAdress(): ?string
    {
        return $this->delivery_adress;
    }

    /**
     * Set the delivery address of the user.
     *
     * @param string|null $delivery_adress The new delivery address
     * @return static
     */
    public function setDeliveryAdress(?string $delivery_adress): static
    {
        $this->delivery_adress = $delivery_adress;
        return $this;
    }

    /**
     * Get the name of the user.
     *
     * @return string|null The user name
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the name of the user.
     *
     * @param string $name The new user name
     * @return static
     */
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Check if the user's email is verified.
     *
     * @return bool True if verified, false otherwise
     */
    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    /**
     * Set the email verification status of the user.
     *
     * @param bool $isVerified The new verification status
     * @return static
     */
    public function setVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;
        return $this;
    }
}