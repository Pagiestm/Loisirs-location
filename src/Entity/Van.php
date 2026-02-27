<?php

namespace App\Entity;

use App\Repository\VanRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Attribute as Vich;

#[ORM\Entity(repositoryClass: VanRepository::class)]
#[ORM\Table(name: 'vans')]
#[Vich\Uploadable]
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

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageName = null;

    // Champ non persisté — contient le fichier uploadé
    #[Vich\UploadableField(mapping: 'van_image', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    #[ORM\Column(name: 'created_at')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(name: 'updated_at')]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Quote>
     */
    #[ORM\OneToMany(targetEntity: Quote::class, mappedBy: 'van')]
    private Collection $quotes;

    /**
     * @var Collection<int, VanOption>
     */
    #[ORM\OneToMany(targetEntity: VanOption::class, mappedBy: 'van', cascade: ['persist', 'remove'])]
    private Collection $vanOptions;

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
        $this->quotes = new ArrayCollection();
        $this->vanOptions = new ArrayCollection();
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

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): static
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * L'injection d'un fichier met automatiquement updatedAt à jour
     * pour déclencher les lifecycle events Doctrine.
     */
    public function setImageFile(?File $imageFile = null): static
    {
        $this->imageFile = $imageFile;

        if ($imageFile !== null) {
            $this->updatedAt = new \DateTimeImmutable();
        }

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

    /**
     * @return Collection<int, Quote>
     */
    public function getQuotes(): Collection
    {
        return $this->quotes;
    }

    public function addQuote(Quote $quote): static
    {
        if (!$this->quotes->contains($quote)) {
            $this->quotes->add($quote);
            $quote->setVan($this);
        }

        return $this;
    }

    public function removeQuote(Quote $quote): static
    {
        if ($this->quotes->removeElement($quote)) {
            // set the owning side to null (unless already changed)
            if ($quote->getVan() === $this) {
                $quote->setVan(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, VanOption>
     */
    public function getVanOptions(): Collection
    {
        return $this->vanOptions;
    }

    public function addVanOption(VanOption $vanOption): static
    {
        if (!$this->vanOptions->contains($vanOption)) {
            $this->vanOptions->add($vanOption);
            $vanOption->setVan($this);
        }

        return $this;
    }

    public function removeVanOption(VanOption $vanOption): static
    {
        if ($this->vanOptions->removeElement($vanOption)) {
            // set the owning side to null (unless already changed)
            if ($vanOption->getVan() === $this) {
                $vanOption->setVan(null);
            }
        }

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
}
