<?php

namespace App\Entity\Quote;

use App\Repository\Quote\QuoteResponseValueRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Attribute as Vich;

#[ORM\Entity(repositoryClass: QuoteResponseValueRepository::class)]
#[ORM\Table(name: 'quote_response_values')]
#[Vich\Uploadable]
class QuoteResponseValue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Field::class, inversedBy: 'quoteResponseValues')]
    #[ORM\JoinColumn(name: 'id_field', referencedColumnName: 'id_field', nullable: false)]
    private ?Field $field = null;

    #[ORM\ManyToOne(targetEntity: QuoteResponse::class, inversedBy: 'quoteResponseValues')]
    #[ORM\JoinColumn(name: 'id_quote_response', referencedColumnName: 'id_quote_response', nullable: false)]
    private ?QuoteResponse $quoteResponse = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $value = null;

    #[Vich\UploadableField(mapping: 'quote_response_file', fileNameProperty: 'value')]
    private ?File $documentFile = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

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

    public function setValue(?string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getDocumentFile(): ?File
    {
        return $this->documentFile;
    }

    public function setDocumentFile(?File $documentFile = null): void
    {
        $this->documentFile = $documentFile;

        if (null !== $documentFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
