<?php

namespace App\Twig\Components\Home;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'components/home/Reviews.html.twig')]
final class Reviews
{
    public array $placeData = [];
}
