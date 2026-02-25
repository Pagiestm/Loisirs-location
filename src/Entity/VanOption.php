<?php

namespace App\Entity;

use App\Repository\VanOptionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VanOptionRepository::class)]
#[ORM\Table(name: 'van_options')]
class VanOption
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Van::class, inversedBy: 'vanOptions')]
    #[ORM\JoinColumn(name: 'id_van', referencedColumnName: 'id_van', nullable: false)]
    private ?Van $van = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Option::class, inversedBy: 'vanOptions')]
    #[ORM\JoinColumn(name: 'id_option', referencedColumnName: 'id_option', nullable: false)]
    private ?Option $option = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $value = null;

    public function getVan(): ?Van
    {
        return $this->van;
    }

    public function setVan(?Van $van): static
    {
        $this->van = $van;

        return $this;
    }

    public function getOption(): ?Option
    {
        return $this->option;
    }

    public function setOption(?Option $option): static
    {
        $this->option = $option;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): static
    {
        $this->value = $value;

        return $this;
    }
}
