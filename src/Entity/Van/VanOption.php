<?php

namespace App\Entity\Van;

use App\Entity\Van\Option;
use App\Repository\Van\VanOptionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VanOptionRepository::class)]
#[ORM\Table(name: 'van_options', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'UNIQ_VAN_OPTION', columns: ['id_van', 'id_option'])
])]
class VanOption
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_van_option')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Van::class, inversedBy: 'options')]
    #[ORM\JoinColumn(name: 'id_van', referencedColumnName: 'id_van', nullable: false)]
    private ?Van $van = null;

    #[ORM\ManyToOne(targetEntity: Option::class, inversedBy: 'vanOptions')]
    #[ORM\JoinColumn(name: 'id_option', referencedColumnName: 'id_option', nullable: false)]
    private ?Option $option = null;

    #[ORM\Column(length: 50, nullable: false)]
    private ?string $value = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function __toString()
    {
        return $this->getOption()->getName();
    }
}
