<?php

namespace App\Twig\Components\Vans;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'components/vans/PageHero.html.twig')]
final class PageHero
{
    public int $count = 0;
}
