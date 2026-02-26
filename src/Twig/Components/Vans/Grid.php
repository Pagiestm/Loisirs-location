<?php

namespace App\Twig\Components\Vans;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'components/vans/Grid.html.twig')]
final class Grid
{
    public array $vans = [];
}
