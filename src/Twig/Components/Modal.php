<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Modal
{
    public string $id;
    public string $title = '';
    public string $size = 'md'; // sm, md, lg, xl
    public bool $initiallyOpen = false;
}
