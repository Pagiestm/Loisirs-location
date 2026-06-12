<?php

namespace App\Entity\Quote;

use App\Entity\Van\Van;
use App\Repository\Quote\QuoteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuoteRepository::class)]
#[ORM\Table(name: 'quotes')]
class Quote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_quote')]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(targetEntity: Van::class, mappedBy: 'quote')]
    private Collection $vans;

    /**
     * @var Collection<int, Field>
     */
    #[ORM\OneToMany(targetEntity: Field::class, mappedBy: 'quote', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $fields;

    /**
     * @var Collection<int, QuoteResponse>
     */
    #[ORM\OneToMany(targetEntity: QuoteResponse::class, mappedBy: 'quote')]
    private Collection $quoteResponses;

    public function __construct()
    {
        $this->fields = new ArrayCollection();
        $this->quoteResponses = new ArrayCollection();
        $this->vans = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Van>
     */
    public function getVans(): Collection
    {
        return $this->vans;
    }

    public function addVan(Van $van): static
    {
        if (!$this->vans->contains($van)) {
            $this->vans->add($van);
            $van->setQuote($this);
        }

        return $this;
    }

    public function removeVan(Van $van): static
    {
        if ($this->vans->removeElement($van)) {
            // set the owning side to null (unless already changed)
            if ($van->getQuote() === $this) {
                $van->setQuote(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Field>
     */
    public function getFields(): Collection
    {
        return $this->fields;
    }

    public function addField(Field $field): static
    {
        if (!$this->fields->contains($field)) {
            $this->fields->add($field);
            $field->setQuote($this);
        }

        return $this;
    }

    public function removeField(Field $field): static
    {
        if ($this->fields->removeElement($field)) {
            // set the owning side to null (unless already changed)
            if ($field->getQuote() === $this) {
                $field->setQuote(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, QuoteResponse>
     */
    public function getQuoteResponses(): Collection
    {
        return $this->quoteResponses;
    }

    public function addQuoteResponse(QuoteResponse $quoteResponse): static
    {
        if (!$this->quoteResponses->contains($quoteResponse)) {
            $this->quoteResponses->add($quoteResponse);
            $quoteResponse->setQuote($this);
        }

        return $this;
    }

    public function removeQuoteResponse(QuoteResponse $quoteResponse): static
    {
        if ($this->quoteResponses->removeElement($quoteResponse)) {
            // set the owning side to null (unless already changed)
            if ($quoteResponse->getQuote() === $this) {
                $quoteResponse->setQuote(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name ?? 'Devis #' . $this->id;
    }
}
