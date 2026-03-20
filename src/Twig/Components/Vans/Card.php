<?php

namespace App\Twig\Components\Vans;

use App\Entity\Van\Van;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'components/vans/Card.html.twig')]
final class Card
{
    public Van $van;
}
