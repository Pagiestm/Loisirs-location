<?php

namespace App\Entity\Van;

use App\Entity\Van\VanEquipment;
use App\Repository\Van\EquipmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EquipmentRepository::class)]
#[ORM\Table(name: 'equipments')]
class Equipment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_equipment')]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $icon = null;

    /**
     * @var Collection<int, VanEquipment>
     */
    #[ORM\OneToMany(targetEntity: VanEquipment::class, mappedBy: 'equipment', cascade: ['persist', 'remove'])]
    private Collection $vanEquipments;

    public function __construct()
    {
        $this->vanEquipments = new ArrayCollection();
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

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): static
    {
        if ($icon === null || '' === trim($icon)) {
            $this->icon = null;

            return $this;
        }

        $normalized = trim($icon);
        $this->icon = str_contains($normalized, ':') ? $normalized : 'lucide:' . $normalized;

        return $this;
    }

    /**
     * @return Collection<int, VanEquipment>
     */
    public function getVanEquipments(): Collection
    {
        return $this->vanEquipments;
    }

    public function addVanEquipment(VanEquipment $vanEquipment): static
    {
        if (!$this->vanEquipments->contains($vanEquipment)) {
            $this->vanEquipments->add($vanEquipment);
            $vanEquipment->setEquipment($this);
        }

        return $this;
    }

    public function removeVanEquipment(VanEquipment $vanEquipment): static
    {
        if ($this->vanEquipments->removeElement($vanEquipment)) {
            // set the owning side to null (unless already changed)
            if ($vanEquipment->getEquipment() === $this) {
                $vanEquipment->setEquipment(null);
            }
        }

        return $this;
    }
}
