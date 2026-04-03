<?php

namespace App\Twig\Components\Blog;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'components/blog/Grid.html.twig')]
final class Grid
{
    /**
     * @var iterable<mixed>
     */
    public iterable $posts = [];
}
