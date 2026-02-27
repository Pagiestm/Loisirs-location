<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class StrongPassword extends Constraint
{
    public string $tooShortMessage = 'Le mot de passe doit contenir au moins {{ limit }} caractères.';
    public string $missingUppercaseMessage = 'Le mot de passe doit contenir au moins une lettre majuscule.';
    public string $missingLowercaseMessage = 'Le mot de passe doit contenir au moins une lettre minuscule.';
    public string $missingDigitMessage = 'Le mot de passe doit contenir au moins un chiffre.';
    public string $missingSpecialCharMessage = 'Le mot de passe doit contenir au moins un caractère spécial (!@#$%^&*(),.?":{}|<>).';

    public int $minLength = 8;

    public function validatedBy(): string
    {
        return static::class . 'Validator';
    }
}
