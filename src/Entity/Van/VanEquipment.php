<?php

namespace App\Entity\Van;

use App\Entity\Van\Equipment;
use App\Repository\Van\VanEquipmentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VanEquipmentRepository::class)]
#[ORM\Table(name: 'van_equipments', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'UNIQ_VAN_EQUIPMENT', columns: ['id_van', 'id_equipment'])
])]
class VanEquipment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_van_equipment')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Van::class, inversedBy: 'options')]
    #[ORM\JoinColumn(name: 'id_van', referencedColumnName: 'id_van', nullable: false)]
    private ?Van $van = null;

    #[ORM\ManyToOne(targetEntity: Equipment::class, inversedBy: 'vanEquipments')]
    #[ORM\JoinColumn(name: 'id_equipment', referencedColumnName: 'id_equipment', nullable: false)]
    private ?Equipment $equipment = null;

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

    public function getEquipment(): ?Equipment
    {
        return $this->equipment;
    }

    public function setEquipment(?Equipment $equipment): static
    {
        $this->equipment = $equipment;

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
        return $this->getEquipment()->getName();
    }
}
