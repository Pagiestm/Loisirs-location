<?php

namespace App\Twig\Components\Home;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'components/home/Vans.html.twig')]
final class Vans
{
    /** @var array<\App\Entity\Van\Van> */
    public array $vans = [];
}
