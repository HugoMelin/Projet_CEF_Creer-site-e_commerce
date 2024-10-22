<?php

namespace App\Entity;

use App\Repository\SweatShirtRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * SweatShirt entity represents a sweatshirt product in the application.
 */
#[ORM\Entity(repositoryClass: SweatShirtRepository::class)]
class SweatShirt
{
    /**
     * @var int|null The unique identifier for the sweatshirt
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var string|null The name of the sweatshirt
     */
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var string|null The size of the sweatshirt
     */
    #[ORM\Column(length: 255)]
    private ?string $size = null;

    /**
     * @var int|null The stock quantity for extra small size
     */
    #[ORM\Column]
    private ?int $stock_xs = null;

    /**
     * @var int|null The stock quantity for small size
     */
    #[ORM\Column]
    private ?int $stock_s = null;

    /**
     * @var int|null The stock quantity for medium size
     */
    #[ORM\Column]
    private ?int $stock_m = null;

    /**
     * @var int|null The stock quantity for large size
     */
    #[ORM\Column]
    private ?int $stock_l = null;

    /**
     * @var int|null The stock quantity for extra large size
     */
    #[ORM\Column]
    private ?int $stock_xl = null;

    /**
     * @var string|null The price of the sweatshirt
     */
    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $price = null;

    /**
     * @var bool|null Indicates if the sweatshirt is a top product
     */
    #[ORM\Column(options: ['default' => false])]
    private ?bool $top = false;

    /**
     * @var Collection<int, Image> Collection of images associated with the sweatshirt
     */
    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'idsweatshirt')]
    private Collection $images;

    /**
     * SweatShirt constructor.
     */
    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    /**
     * Get the ID of the sweatshirt.
     *
     * @return int|null The sweatshirt ID
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the name of the sweatshirt.
     *
     * @return string|null The sweatshirt name
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the name of the sweatshirt.
     *
     * @param string $name The new sweatshirt name
     * @return static
     */
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get the size of the sweatshirt.
     *
     * @return string|null The sweatshirt size
     */
    public function getSize(): ?string
    {
        return $this->size;
    }

    /**
     * Set the size of the sweatshirt.
     *
     * @param string $size The new sweatshirt size
     * @return static
     */
    public function setSize(string $size): static
    {
        $this->size = $size;
        return $this;
    }

    /**
     * Get the stock quantity for extra small size.
     *
     * @return int|null The stock quantity for XS
     */
    public function getStockXs(): ?int
    {
        return $this->stock_xs;
    }

    /**
     * Set the stock quantity for extra small size.
     *
     * @param int $stock_xs The new stock quantity for XS
     * @return static
     */
    public function setStockXs(int $stock_xs): static
    {
        $this->stock_xs = $stock_xs;
        return $this;
    }

    /**
     * Get the stock quantity for small size.
     *
     * @return int|null The stock quantity for S
     */
    public function getStockS(): ?int
    {
        return $this->stock_s;
    }

    /**
     * Set the stock quantity for small size.
     *
     * @param int $stock_s The new stock quantity for S
     * @return static
     */
    public function setStockS(int $stock_s): static
    {
        $this->stock_s = $stock_s;
        return $this;
    }

    /**
     * Get the stock quantity for medium size.
     *
     * @return int|null The stock quantity for M
     */
    public function getStockM(): ?int
    {
        return $this->stock_m;
    }

    /**
     * Set the stock quantity for medium size.
     *
     * @param int $stock_m The new stock quantity for M
     * @return static
     */
    public function setStockM(int $stock_m): static
    {
        $this->stock_m = $stock_m;
        return $this;
    }

    /**
     * Get the stock quantity for large size.
     *
     * @return int|null The stock quantity for L
     */
    public function getStockL(): ?int
    {
        return $this->stock_l;
    }

    /**
     * Set the stock quantity for large size.
     *
     * @param int $stock_l The new stock quantity for L
     * @return static
     */
    public function setStockL(int $stock_l): static
    {
        $this->stock_l = $stock_l;
        return $this;
    }

    /**
     * Get the stock quantity for extra large size.
     *
     * @return int|null The stock quantity for XL
     */
    public function getStockXl(): ?int
    {
        return $this->stock_xl;
    }

    /**
     * Set the stock quantity for extra large size.
     *
     * @param int $stock_xl The new stock quantity for XL
     * @return static
     */
    public function setStockXl(int $stock_xl): static
    {
        $this->stock_xl = $stock_xl;
        return $this;
    }

    /**
     * Get the price of the sweatshirt.
     *
     * @return string|null The sweatshirt price
     */
    public function getPrice(): ?string
    {
        return $this->price;
    }

    /**
     * Set the price of the sweatshirt.
     *
     * @param string $price The new sweatshirt price
     * @return static
     */
    public function setPrice(string $price): static
    {
        $this->price = $price;
        return $this;
    }

    /**
     * Check if the sweatshirt is a top product.
     *
     * @return bool|null True if it's a top product, false otherwise
     */
    public function isTop(): ?bool
    {
        return $this->top;
    }

    /**
     * Set whether the sweatshirt is a top product.
     *
     * @param bool $top True to set as top product, false otherwise
     * @return static
     */
    public function setTop(bool $top): static
    {
        $this->top = $top;
        return $this;
    }

    /**
     * Get the collection of images associated with the sweatshirt.
     *
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    /**
     * Add an image to the sweatshirt.
     *
     * @param Image $image The image to add
     * @return static
     */
    public function addImage(Image $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setIdsweatshirt($this);
        }
        return $this;
    }

    /**
     * Remove an image from the sweatshirt.
     *
     * @param Image $image The image to remove
     * @return static
     */
    public function removeImage(Image $image): static
    {
        if ($this->images->removeElement($image)) {
            if ($image->getIdsweatshirt() === $this) {
                $image->setIdsweatshirt(null);
            }
        }
        return $this;
    }
}