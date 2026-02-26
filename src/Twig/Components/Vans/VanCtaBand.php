<?php

namespace App\Twig\Components\Vans;

use App\Entity\Van;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'components/vans/VanCtaBand.html.twig')]
final class VanCtaBand
{
    public Van $van;
}
