<?php

namespace App\Entity\Quote;

use App\Enum\FieldEnum;
use App\Repository\Quote\FieldRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: FieldRepository::class)]
#[ORM\Table(name: 'fields')]
class Field
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_field')]
    private ?int $id = null;

    #[ORM\Column(type: 'json')]
    private array $options = [];

    #[Gedmo\SortablePosition]
    #[ORM\Column(name: 'position', type: 'integer')]
    private ?int $position = null;

    #[ORM\Column(length: 100, nullable: false, type: Types::ENUM)]
    private FieldEnum $type;

    #[ORM\ManyToOne(targetEntity: Quote::class, inversedBy: 'fields')]
    #[ORM\JoinColumn(name: 'id_quote', referencedColumnName: 'id_quote', nullable: false, onDelete: 'CASCADE')]
    private ?Quote $quote = null;

    /**
     * @var Collection<int, QuoteResponseValue>
     */
    #[ORM\OneToMany(targetEntity: QuoteResponseValue::class, mappedBy: 'field', orphanRemoval: true)]
    private Collection $quoteResponseValues;

    public function __construct()
    {
        $this->quoteResponseValues = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getType(): FieldEnum
    {
        return $this->type;
    }

    public function setType(FieldEnum $type): static
    {
        $this->type = $type;

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
     * @return Collection<int, QuoteResponseValue>
     */
    public function getQuoteResponseValues(): Collection
    {
        return $this->quoteResponseValues;
    }

    public function addQuoteResponseValue(QuoteResponseValue $quoteResponseValue): static
    {
        if (!$this->quoteResponseValues->contains($quoteResponseValue)) {
            $this->quoteResponseValues->add($quoteResponseValue);
            $quoteResponseValue->setField($this);
        }

        return $this;
    }

    public function removeQuoteResponseValue(QuoteResponseValue $quoteResponseValue): static
    {
        if ($this->quoteResponseValues->removeElement($quoteResponseValue)) {
            // set the owning side to null (unless already changed)
            if ($quoteResponseValue->getField() === $this) {
                $quoteResponseValue->setField(null);
            }
        }

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->options['label'] ?? null;
    }

    public function __toString()
    {
        return $this->getLabel() ?? 'Champ #' . $this->getId();
    }
}
