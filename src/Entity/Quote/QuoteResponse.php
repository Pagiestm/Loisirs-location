<?php

namespace App\Entity\Quote;

use App\Entity\User;
use App\Entity\Van\Van;
use App\Repository\Quote\QuoteResponseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuoteResponseRepository::class)]
#[ORM\Table(name: 'quote_responses')]
class QuoteResponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_quote_response')]
    private ?int $id = null;

    #[ORM\Column(name: 'created_at')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(name: 'updated_at')]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: Quote::class, inversedBy: 'quoteResponses')]
    #[ORM\JoinColumn(name: 'id_quote', referencedColumnName: 'id_quote', nullable: false)]
    private ?Quote $quote = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'quoteResponses')]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user', nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Van::class, inversedBy: 'quoteResponses')]
    #[ORM\JoinColumn(name: 'id_van', referencedColumnName: 'id_van', nullable: true)]
    private ?Van $van = null;

    /**
     * @var Collection<int, QuoteResponseValue>
     */
    #[ORM\OneToMany(targetEntity: QuoteResponseValue::class, mappedBy: 'quoteResponse', cascade: ['persist', 'remove'])]
    private Collection $quoteResponseValues;

    public function __construct()
    {
        $this->quoteResponseValues = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getQuote(): ?Quote
    {
        return $this->quote;
    }

    public function setQuote(?Quote $quote): static
    {
        $this->quote = $quote;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

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
            $quoteResponseValue->setQuoteResponse($this);
        }

        return $this;
    }

    public function removeQuoteResponseValue(QuoteResponseValue $quoteResponseValue): static
    {
        if ($this->quoteResponseValues->removeElement($quoteResponseValue)) {
            // set the owning side to null (unless already changed)
            if ($quoteResponseValue->getQuoteResponse() === $this) {
                $quoteResponseValue->setQuoteResponse(null);
            }
        }

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
}
