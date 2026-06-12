<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NewsletterExtension extends AbstractExtension
{
    public function __construct(
        private string $newsletterSecret
    ) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('unsubscribe_token', [$this, 'generateToken']),
        ];
    }

    public function generateToken(string $email): string
    {
        return hash_hmac('sha256', $email, $this->newsletterSecret);
    }
}
