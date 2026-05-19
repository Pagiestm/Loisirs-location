<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class UploadController extends AbstractController
{
    #[Route('/uploads/quotes/{filename}', name: 'admin_upload_quote_file', methods: ['GET'])]
    public function serveQuoteFile(string $filename, ParameterBagInterface $params): Response
    {
        $projectDir = $params->get('kernel.project_dir');
        $filePath = $projectDir . '/uploads/quotes/' . $filename;

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('Le fichier demandé n\'existe pas.');
        }

        $response = new BinaryFileResponse($filePath);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $filename
        );

        return $response;
    }
}
