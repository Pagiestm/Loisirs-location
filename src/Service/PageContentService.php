<?php

namespace App\Service;

use Symfony\Component\HttpKernel\KernelInterface;

class PageContentService
{
    private string $storageDir;

    public function __construct(KernelInterface $kernel)
    {
        $this->storageDir = $kernel->getProjectDir() . '/templates/editable_pages';
        if (!is_dir($this->storageDir)) {
            mkdir($this->storageDir, 0755, true);
        }
    }

    public function load(string $pageId): string
    {
        $filePath = $this->storageDir . DIRECTORY_SEPARATOR . $pageId . '.html';
        if (!file_exists($filePath)) {
            return '';
        }
        return file_get_contents($filePath);
    }

    public function save(string $pageId, string $content): void
    {
        $filePath = $this->storageDir . DIRECTORY_SEPARATOR . $pageId . '.html';
        file_put_contents($filePath, $content);
    }
}
