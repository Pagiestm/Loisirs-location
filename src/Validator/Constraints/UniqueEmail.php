<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class UniqueEmail extends Constraint
{
    public string $message = 'Cet email "{{ value }}" est déjà utilisé.';
    public string $repo = 'user';

    public function __construct(
        ?string $repo = null,
        ?string $message = null,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        $this->repo = $repo ?? $this->repo;
        $this->message = $message ?? $this->message;

        parent::__construct(null, $groups, $payload);
    }

    public function validatedBy(): string
    {
        return static::class . 'Validator';
    }
}
