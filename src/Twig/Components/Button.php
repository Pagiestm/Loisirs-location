<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class Button
{
    public string $label = '';
    public ?string $href = null;
    public string $variant = 'primary';
    public string $size = 'md';
    public bool $fullWidth = false;
    public string $type = 'button';
    public ?string $icon = null;
    public ?string $ariaLabel = null;
}
