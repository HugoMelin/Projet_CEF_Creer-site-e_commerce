<?php

namespace App\Entity;

use App\Repository\SweatShirtRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SweatShirtRepository::class)]
class SweatShirt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $size = null;

    #[ORM\Column]
    private ?int $stock_xs = null;

    #[ORM\Column]
    private ?int $stock_s = null;

    #[ORM\Column]
    private ?int $stock_m = null;

    #[ORM\Column]
    private ?int $stock_l = null;

    #[ORM\Column]
    private ?int $stock_xl = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column]
    private ?bool $top = null;

    /**
     * @var Collection<int, Image>
     */
    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'idsweatshirt')]
    private Collection $images;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(string $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getStockXs(): ?int
    {
        return $this->stock_xs;
    }

    public function setStockXs(int $stock_xs): static
    {
        $this->stock_xs = $stock_xs;

        return $this;
    }

    public function getStockS(): ?int
    {
        return $this->stock_s;
    }

    public function setStockS(int $stock_s): static
    {
        $this->stock_s = $stock_s;

        return $this;
    }

    public function getStockM(): ?int
    {
        return $this->stock_m;
    }

    public function setStockM(int $stock_m): static
    {
        $this->stock_m = $stock_m;

        return $this;
    }

    public function getStockL(): ?int
    {
        return $this->stock_l;
    }

    public function setStockL(int $stock_l): static
    {
        $this->stock_l = $stock_l;

        return $this;
    }

    public function getStockXl(): ?int
    {
        return $this->stock_xl;
    }

    public function setStockXl(int $stock_xl): static
    {
        $this->stock_xl = $stock_xl;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function isTop(): ?bool
    {
        return $this->top;
    }

    public function setTop(bool $top): static
    {
        $this->top = $top;

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setIdsweatshirt($this);
        }

        return $this;
    }

    public function removeImage(Image $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getIdsweatshirt() === $this) {
                $image->setIdsweatshirt(null);
            }
        }

        return $this;
    }
}
