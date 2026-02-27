<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class UniqueEmail extends Constraint
{
    public string $message = 'Cet email "{{ value }}" est déjà utilisé.';

    public function validatedBy(): string
    {
        return static::class . 'Validator';
    }
}
