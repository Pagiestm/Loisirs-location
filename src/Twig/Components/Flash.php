<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('Flash')]
class Flash
{
    public string $type = 'info';
    public string $message = '';
}
