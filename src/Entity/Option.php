<?php

namespace App\Entity;

use App\Repository\OptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OptionRepository::class)]
#[ORM\Table(name: 'options')]
class Option
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_option')]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $icon = null;

    /**
     * @var Collection<int, VanOption>
     */
    #[ORM\OneToMany(targetEntity: VanOption::class, mappedBy: 'option', cascade: ['persist', 'remove'])]
    private Collection $vanOptions;

    public function __construct()
    {
        $this->vanOptions = new ArrayCollection();
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
        $this->icon = $icon;

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
            $vanOption->setOption($this);
        }

        return $this;
    }

    public function removeVanOption(VanOption $vanOption): static
    {
        if ($this->vanOptions->removeElement($vanOption)) {
            // set the owning side to null (unless already changed)
            if ($vanOption->getOption() === $this) {
                $vanOption->setOption(null);
            }
        }

        return $this;
    }
}
