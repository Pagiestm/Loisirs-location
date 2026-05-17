<?php

namespace App\Entity\Van;

use App\Entity\Quote\Quote;
use App\Entity\Rental;
use App\Repository\Van\VanRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VanRepository::class)]
#[ORM\Table(name: 'vans')]
class Van
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_van')]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    private ?string $subtitle = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'created_at')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(name: 'updated_at')]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: Quote::class, inversedBy: 'vans')]
    #[ORM\JoinColumn(name: 'id_quote', referencedColumnName: 'id_quote', nullable: true)]
    private ?Quote $quote = null;

    /**
     * @var Collection<int, VanOption>
     */
    #[ORM\OneToMany(targetEntity: VanOption::class, mappedBy: 'van', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $options;

    /**
     * @var Collection<int, Rental>
     */
    #[ORM\OneToMany(targetEntity: Rental::class, mappedBy: 'van')]
    private Collection $rentals;

    /**
     * @var Collection<int, VanImage>
     */
    #[ORM\OneToMany(targetEntity: VanImage::class, mappedBy: 'van', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $images;

    public function __construct()
    {
        $this->options = new ArrayCollection();
        $this->rentals = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
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

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setSubtitle(string $subtitle): static
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getQuote(): ?Quote
    {
        return $this->quote;
    }

    public function setQuote(?Quote $quote): static
    {
        $this->quote = $quote;
        return $this;
    }

    /**
     * @return Collection<int, VanOption>
     */
    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function addOption(VanOption $vanOption): static
    {
        if (!$this->options->contains($vanOption)) {
            $this->options->add($vanOption);
            $vanOption->setVan($this);
        }

        return $this;
    }

    public function removeOption(VanOption $vanOption): static
    {
        $this->options->removeElement($vanOption);

        return $this;
    }

    /**
     * @return Collection<int, Rental>
     */
    public function getRentals(): Collection
    {
        return $this->rentals;
    }

    public function addRental(Rental $rental): static
    {
        if (!$this->rentals->contains($rental)) {
            $this->rentals->add($rental);
            $rental->setVan($this);
        }

        return $this;
    }

    public function removeRental(Rental $rental): static
    {
        if ($this->rentals->removeElement($rental)) {
            // set the owning side to null (unless already changed)
            if ($rental->getVan() === $this) {
                $rental->setVan(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, VanImage>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(VanImage $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setVan($this);
        }

        return $this;
    }

    public function removeImage(VanImage $image): static
    {
        $this->images->removeElement($image);

        return $this;
    }

    public function isQuoteAvailable(): bool
    {
        return $this->quote !== null;
    }
}
