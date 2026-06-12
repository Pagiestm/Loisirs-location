<?php

namespace App\Twig\Components\Editable;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class Page extends AbstractController
{
    use DefaultActionTrait, ComponentToolsTrait;

    #[LiveProp]
    public string $page = 'home';

    #[LiveProp(url: true)]
    public bool $edit = false;

    #[LiveProp(writable: true)]
    public string $content = '';

    public function mount(string $page): void
    {
        $this->page = $page;
        $this->content = $this->loadContent();
    }

    private function loadContent(): string
    {
        $filePath = $this->getPageFilePath();
        if (file_exists($filePath)) {
            return file_get_contents($filePath);
        }
        return '';
    }

    private function getPageFilePath(): string
    {
        return $this->getParameter('pages_directory')
            . DIRECTORY_SEPARATOR
            . $this->page
            . '.html';
    }

    #[LiveAction]
    public function saveContent(): void
    {
        $filePath = $this->getPageFilePath();
        $dir = dirname($filePath);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, recursive: true);
        }

        file_put_contents($filePath, $this->content);
        $this->dispatchBrowserEvent('page-editor:saved');
    }

    #[Route('/upload/image', name: 'upload_image', methods: ['POST'])]
    public function uploadImage(Request $request): JsonResponse
    {
        $file = $request->files->get('file');

        if (!$file) {
            return new JsonResponse(['error' => 'No file'], 400);
        }

        if (!in_array($file->guessExtension(), ['jpg', 'jpeg', 'png', 'webp'])) {
            return new JsonResponse(['error' => 'Invalid file'], 400);
        }

        $filename = uniqid() . '.' . $file->guessExtension();

        $file->move(
            $this->getParameter('kernel.project_dir') . '/public/uploads/pages',
            $filename
        );

        return new JsonResponse([
            'location' => '/uploads/pages/' . $filename
        ]);
    }
}
