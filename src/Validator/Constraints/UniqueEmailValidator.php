<?php

namespace App\Validator\Constraints;

use App\Repository\NewsletterRepository;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class UniqueEmailValidator extends ConstraintValidator
{
    public function __construct(
        private UserRepository $userRepository,
        private NewsletterRepository $newsletterRepository
    ) {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueEmail) {
            throw new UnexpectedTypeException($constraint, UniqueEmail::class);
        }

        // Custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        if ($constraint->repo === 'user') {
            $existingUser = $this->userRepository->findOneBy(['email' => $value]);
        } elseif ($constraint->repo === 'newsletter') {
            $existingUser = $this->newsletterRepository->findOneBy(['email' => $value]);
        } else {
            throw new UnexpectedValueException($constraint->repo, 'string (either "user" or "newsletter")');
        }

        if ($existingUser !== null) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
