<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class TextureBackground
{
    /** 'wood' | 'sand' */
    public string $type = 'wood';

    /** Opacité de l'overlay sombre par-dessus la texture (0.0 → 1.0) */
    public float $overlay = 0.72;

    /** Intensité de la texture elle-même (0.0 → 1.0) — la couleur de fond reste prioritaire */
    public float $intensity = 1.0;
}
