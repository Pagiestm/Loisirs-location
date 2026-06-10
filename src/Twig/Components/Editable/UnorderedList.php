<?php

namespace App\Twig\Components\Editable;

use App\Service\YamlService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class UnorderedList extends AbstractController
{
    use DefaultActionTrait, ComponentToolsTrait;

    const SEPARATOR = '||';

    #[LiveProp]
    public string $page = 'messages';

    #[LiveProp]
    public string $field;

    #[LiveProp]
    public string $placeholder = '';

    #[LiveProp(writable: true)]
    public array $items = [];

    #[LiveProp(url: true)]
    public bool $edit = false;

    #[LiveProp]
    public string $class = '';

    public function __construct(
        private readonly TranslatorInterface $translator
    ) {}

    public function mount(string $field, string $page): void
    {
        $this->field = $field;
        $this->page = $page;

        $translate = $this->translator->trans($field, domain: $page, locale: 'fr');
        $raw = $translate !== $field ? $translate : '';

        $this->items = $raw !== ''
            ? explode(self::SEPARATOR, $raw)
            : [];
    }

    #[LiveAction]
    public function addItem(): void
    {
        $this->items[] = '';
    }

    #[LiveAction]
    public function removeItem(#[LiveArg] int $index): void
    {
        array_splice($this->items, $index, 1);

        if (empty($this->items)) {
            $this->items = [''];
        }
    }

    #[LiveAction]
    public function saveContent(YamlService $yamlService): void
    {
        $filtered = array_values(array_filter($this->items, fn($i) => trim($i) !== ''));
        $value = implode(self::SEPARATOR, $filtered);

        $yamlService->write(
            $this->getParameter('translations_pages_directory') . DIRECTORY_SEPARATOR . $this->page . '.fr.yaml',
            [$this->field => $value]
        );

        $this->dispatchBrowserEvent('context-menu:close');
    }
}
