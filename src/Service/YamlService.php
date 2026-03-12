<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class YamlService
{
    public function write(string $filePath, array $data): void
    {
        $this->createFileIfNotExist($filePath);
        $parsedData = Yaml::parseFile($filePath) ?? [];
        $mergedData = array_merge($parsedData, $data);
        $yamlContent = Yaml::dump($mergedData);
        file_put_contents($filePath, $yamlContent);
    }

    private function createFileIfNotExist(string $filePath): void
    {
        $filesystem = new Filesystem();
        $exist = $filesystem->exists($filePath);
        if (!$exist) {
            $filesystem->touch($filePath);
        }
    }
}
