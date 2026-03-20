<?php

namespace App\Entity;

use App\Entity\Van\Van;
use App\Repository\RentalRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RentalRepository::class)]
#[ORM\Table(name: 'rentals')]
class Rental
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'rentals')]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user', nullable: false)]
    private ?User $user = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Van::class, inversedBy: 'rentals')]
    #[ORM\JoinColumn(name: 'id_van', referencedColumnName: 'id_van', nullable: false)]
    private ?Van $van = null;

    #[ORM\Column(name: 'rental_start_date')]
    private ?\DateTimeImmutable $rentalStartDate = null;

    #[ORM\Column(name: 'rental_end_date')]
    private ?\DateTimeImmutable $rentalEndDate = null;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getVan(): ?Van
    {
        return $this->van;
    }

    public function setVan(?Van $van): static
    {
        $this->van = $van;

        return $this;
    }

    public function getRentalStartDate(): ?\DateTimeImmutable
    {
        return $this->rentalStartDate;
    }

    public function setRentalStartDate(\DateTimeImmutable $rentalStartDate): static
    {
        $this->rentalStartDate = $rentalStartDate;

        return $this;
    }

    public function getRentalEndDate(): ?\DateTimeImmutable
    {
        return $this->rentalEndDate;
    }

    public function setRentalEndDate(\DateTimeImmutable $rentalEndDate): static
    {
        $this->rentalEndDate = $rentalEndDate;

        return $this;
    }
}
