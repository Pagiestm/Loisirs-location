<?php

namespace App\Twig\Components\Vans;

use App\Entity\Van\Van;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'components/vans/Equipment.html.twig')]
final class Equipment
{
    public Van $van;
}
