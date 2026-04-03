<?php

namespace App\Twig\Components\Blog;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'components/blog/PageHero.html.twig')]
final class PageHero
{
    public int $count = 0;
}
