<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class StrongPasswordValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof StrongPassword) {
            throw new UnexpectedTypeException($constraint, StrongPassword::class);
        }

        // Les contraintes personnalisées doivent ignorer les valeurs null et vides
        // pour permettre aux autres contraintes (NotBlank, NotNull, etc.) de gérer cela
        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        // Vérifier la longueur minimale
        if (strlen($value) < $constraint->minLength) {
            $this->context->buildViolation($constraint->tooShortMessage)
                ->setParameter('{{ limit }}', (string) $constraint->minLength)
                ->addViolation();
            return;
        }

        // Vérifier qu'il y a au moins une lettre majuscule
        if (!preg_match('/[A-Z]/', $value)) {
            $this->context->buildViolation($constraint->missingUppercaseMessage)
                ->addViolation();
        }

        // Vérifier qu'il y a au moins une lettre minuscule
        if (!preg_match('/[a-z]/', $value)) {
            $this->context->buildViolation($constraint->missingLowercaseMessage)
                ->addViolation();
        }

        // Vérifier qu'il y a au moins un chiffre
        if (!preg_match('/[0-9]/', $value)) {
            $this->context->buildViolation($constraint->missingDigitMessage)
                ->addViolation();
        }

        // Vérifier qu'il y a au moins un caractère spécial
        if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $value)) {
            $this->context->buildViolation($constraint->missingSpecialCharMessage)
                ->addViolation();
        }
    }
}
