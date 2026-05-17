<?php

namespace App\Dto\Quote;

class ChoiceOption
{
    public ?string $label = null;
    public ?string $value = null;

    public function __toString(): string
    {
        return $this->label ?? 'Nouvelle option';
    }
}
