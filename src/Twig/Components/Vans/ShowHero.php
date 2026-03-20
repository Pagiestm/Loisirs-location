<?php

namespace App\Twig\Components\Vans;

use App\Entity\Van\Van;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'components/vans/ShowHero.html.twig')]
final class ShowHero
{
    public Van $van;
}
