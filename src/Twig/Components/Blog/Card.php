<?php

namespace App\Twig\Components\Blog;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'components/blog/Card.html.twig')]
final class Card
{
    public mixed $post;
}
