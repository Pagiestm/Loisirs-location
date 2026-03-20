<?php

namespace App\Entity;

use App\Entity\Van\Van;
use App\Repository\QuoteRepository;
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

    #[ORM\ManyToOne(targetEntity: Van::class, inversedBy: 'quotes')]
    #[ORM\JoinColumn(name: 'id_van', referencedColumnName: 'id_van', nullable: false)]
    private ?Van $van = null;

    /**
     * @var Collection<int, Field>
     */
    #[ORM\OneToMany(targetEntity: Field::class, mappedBy: 'quote', cascade: ['persist', 'remove'])]
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

    public function getVan(): ?Van
    {
        return $this->van;
    }

    public function setVan(?Van $van): static
    {
        $this->van = $van;

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
}
