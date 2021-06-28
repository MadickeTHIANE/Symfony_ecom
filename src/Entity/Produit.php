<?php

namespace App\Entity;

use App\Entity\Tag;
use App\Entity\Category;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=ProduitRepository::class)
 */
class Produit
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="integer")
     */
    private $stock;

    /**
     * @ORM\Column(type="text")
     */
    private $description;


    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Tag",inversedBy="produits" ,cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $tags;

    //* le ManyToOne toujours avec inversedBy, et non mappedBy
    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="produit")
     * @ORM\JoinColumn(nullable=true)
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=Reservation::class, mappedBy="produit")
     */
    private $reservations;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->reservations = new ArrayCollection();
    }

    public function getPlaceholder()
    {
        if (!$this->getCategory()) {
            return 'placeholder_none.jpg';
        } else {
            $categoryName = strtolower($this->getCategory()->getName());
            switch ($categoryName) {
                case 'bureau':
                    return 'placeholder_bureau.jpg';
                case 'armoire':
                    return 'placeholder_armoire.jpg';
                case 'canape':
                    return 'placeholder_canape.jpg';
                case 'chaise':
                    return 'placeholder_chaise.jpg';
                case 'lit':
                    return 'placeholder_lit.jpg';
                case 'table':
                    return 'placeholder_table.jpg';
                default:
                    return 'placeholder_none.jpg';
            }
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTag(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function getReservations(): ?Reservation
    {
        return $this->reservations;
    }

    public function setReservations(?Reservation $reservation): self
    {
        $this->reservations = $reservation;

        return $this;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->setProduit($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getProduit() === $this) {
                $reservation->setProduit(null);
            }
        }

        return $this;
    }
}
