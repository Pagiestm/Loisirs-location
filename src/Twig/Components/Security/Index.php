<?php

namespace App\Twig\Components\Security;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/security/Index.html.twig')]
final class Index
{
    use DefaultActionTrait;
    use ComponentToolsTrait;

    #[LiveProp]
    public bool $isLogin = true;

    #[LiveProp]
    public ?string $error = null;

    #[LiveProp]
    public ?string $last_username = null;

    #[LiveAction]
    public function toggleMode(): void
    {
        $this->isLogin = !$this->isLogin;
    }

    #[LiveAction]
    public function handleRegistrationSuccess(): void
    {
        $this->emit('registrationSuccess', componentName: 'Security:Login');
    }
}
