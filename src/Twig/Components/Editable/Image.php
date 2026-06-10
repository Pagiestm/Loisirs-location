<?php

namespace App\Twig\Components\Editable;

use App\Service\YamlService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class Image extends AbstractController
{
    use DefaultActionTrait, ComponentToolsTrait;

    #[LiveProp]
    public string $page = 'messages';

    #[LiveProp]
    public string $field;

    /** Classe CSS passée à l'élément <img> */
    #[LiveProp]
    public string $class = '';

    /** Chemin de l'image (stocké en YAML) */
    #[LiveProp(writable: true)]
    public string $src = '';

    /** Texte alternatif (stocké en YAML) */
    #[LiveProp(writable: true)]
    public string $alt = '';

    #[LiveProp(url: true)]
    public bool $edit = false;

    public function __construct(
        private readonly TranslatorInterface $translator
    ) {}

    public function mount(string $field, string $page): void
    {
        // Clé YAML : <field>.src et <field>.alt
        $srcKey = $field . '.src';
        $altKey = $field . '.alt';

        $src = $this->translator->trans($srcKey, domain: $page, locale: 'fr');
        $alt = $this->translator->trans($altKey, domain: $page, locale: 'fr');

        $this->src   = $src !== $srcKey ? $src : '';
        $this->alt   = $alt !== $altKey ? $alt : '';
        $this->field = $field;
        $this->page  = $page;
    }

    #[LiveAction]
    public function saveImage(Request $request, YamlService $yamlService): void
    {
        $file = $request->files->get('image_file');

        if ($file) {
            $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/images';

            // Supprimer l'ancienne image si elle est dans /uploads/images/
            if (
                $this->src !== ''
                && str_starts_with($this->src, '/uploads/images/')
            ) {
                $oldPath = $this->getParameter('kernel.project_dir') . '/public' . $this->src;
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $filename   = uniqid() . '.' . $file->guessExtension();
            $file->move($uploadsDir, $filename);
            $this->src  = '/uploads/images/' . $filename;
        }

        $dir = $this->getParameter('translations_pages_directory')
            . DIRECTORY_SEPARATOR . $this->page . '.fr.yaml';

        $yamlService->write($dir, [
            $this->field . '.src' => $this->src,
            $this->field . '.alt' => $this->alt,
        ]);

        $this->dispatchBrowserEvent('context-menu:close');
    }
}
