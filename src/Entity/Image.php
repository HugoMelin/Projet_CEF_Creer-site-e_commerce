<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Image entity represents an image associated with a SweatShirt.
 */
#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image
{
    /**
     * @var int|null The unique identifier for the image
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var string|null The name of the image file
     */
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var string|null The URL or path to the image
     */
    #[ORM\Column(length: 255)]
    private ?string $link = null;

    /**
     * @var string|null The alternative text for the image
     */
    #[ORM\Column(type: Types::TEXT)]
    private ?string $alt = null;

    /**
     * @var SweatShirt|null The associated SweatShirt entity
     */
    #[ORM\ManyToOne(inversedBy: 'images')]
    private ?SweatShirt $idsweatshirt = null;

    /**
     * Get the ID of the image.
     *
     * @return int|null The image ID
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the name of the image.
     *
     * @return string|null The image name
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the name of the image.
     *
     * @param string $name The new image name
     * @return static
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the link of the image.
     *
     * @return string|null The image link
     */
    public function getLink(): ?string
    {
        return $this->link;
    }

    /**
     * Set the link of the image.
     *
     * @param string $link The new image link
     * @return static
     */
    public function setLink(string $link): static
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get the alternative text of the image.
     *
     * @return string|null The image alt text
     */
    public function getAlt(): ?string
    {
        return $this->alt;
    }

    /**
     * Set the alternative text of the image.
     *
     * @param string $alt The new image alt text
     * @return static
     */
    public function setAlt(string $alt): static
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Get the associated SweatShirt entity.
     *
     * @return SweatShirt|null The associated SweatShirt
     */
    public function getIdsweatshirt(): ?SweatShirt
    {
        return $this->idsweatshirt;
    }

    /**
     * Set the associated SweatShirt entity.
     *
     * @param SweatShirt|null $idsweatshirt The SweatShirt to associate
     * @return static
     */
    public function setIdsweatshirt(?SweatShirt $idsweatshirt): static
    {
        $this->idsweatshirt = $idsweatshirt;

        return $this;
    }
}
