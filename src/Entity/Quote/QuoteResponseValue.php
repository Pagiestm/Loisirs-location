<?php

namespace App\Entity\Quote;

use App\Repository\Quote\QuoteResponseValueRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuoteResponseValueRepository::class)]
#[ORM\Table(name: 'quote_response_values')]
class QuoteResponseValue
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Field::class, inversedBy: 'quoteResponseValues')]
    #[ORM\JoinColumn(name: 'id_field', referencedColumnName: 'id_field', nullable: false)]
    private ?Field $field = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: QuoteResponse::class, inversedBy: 'quoteResponseValues')]
    #[ORM\JoinColumn(name: 'id_quote_response', referencedColumnName: 'id_quote_response', nullable: false)]
    private ?QuoteResponse $quoteResponse = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $value = null;

    public function getField(): ?Field
    {
        return $this->field;
    }

    public function setField(?Field $field): static
    {
        $this->field = $field;

        return $this;
    }

    public function getQuoteResponse(): ?QuoteResponse
    {
        return $this->quoteResponse;
    }

    public function setQuoteResponse(?QuoteResponse $quoteResponse): static
    {
        $this->quoteResponse = $quoteResponse;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }
}
