<?php

namespace App\Twig\Components\Blog;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'components/blog/RecentPosts.html.twig')]
final class RecentPosts
{
    /**
     * @var iterable<mixed>
     */
    public iterable $posts = [];

    public ?int $currentPostId = null;
}
