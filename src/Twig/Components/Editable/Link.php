<?php

namespace App\Twig\Components\Editable;

use App\Service\YamlService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class Link extends AbstractController
{
    use DefaultActionTrait, ComponentToolsTrait;

    #[LiveProp]
    public string $page = 'messages';

    #[LiveProp]
    public string $field;

    #[LiveProp]
    public string $placeholder = '';

    #[LiveProp(writable: true)]
    public string $value = '';

    #[LiveProp(writable: true)]
    public string $src = '#';

    #[LiveProp]
    public string $class = '';

    #[LiveProp(url: true)]
    public bool $edit = false;

    public function __construct(
        private readonly TranslatorInterface $translator
    ) {}

    public function mount(string $field, string $page): void
    {
        $valueKey = $field . '.value';
        $srcKey = $field . '.url';

        $value = $this->translator->trans($valueKey, domain: $page, locale: 'fr');
        $src = $this->translator->trans($srcKey, domain: $page, locale: 'fr');

        $this->value = $value !== $field . '.value' ? $value : '';
        $this->src = $src !== $field . '.url' ? $src : '';
        $this->field = $field;
        $this->page = $page;
    }

    #[LiveAction]
    public function saveContent(YamlService $yamlService): void
    {
        $yamlService->write($this->getParameter('translations_pages_directory') . DIRECTORY_SEPARATOR . $this->page . '.fr.yaml', [
            $this->field . '.value' => $this->value,
            $this->field . '.url' => $this->src,
        ]);
        $this->dispatchBrowserEvent('context-menu:close');
    }
}
