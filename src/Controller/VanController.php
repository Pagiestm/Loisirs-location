<?php

namespace App\Controller;

use App\Interface\EditableControllerInterface;
use App\Repository\VanRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/nos-vans', name: 'app_vans')]
class VanController extends AbstractController implements EditableControllerInterface
{
    #[Route('', name: '_index')]
    public function index(VanRepository $vanRepository): Response
    {
        return $this->render('pages/vans/index.html.twig', [
            'vans' => $vanRepository->findAllWithOptions(),
        ]);
    }

    #[Route('/{id}', name: '_show', requirements: ['id' => '\d+'])]
    public function show(int $id, VanRepository $vanRepository): Response
    {
        $van = $vanRepository->findOneWithOptions($id);

        if (!$van) {
            throw $this->createNotFoundException('Van introuvable.');
        }

        return $this->render('pages/vans/show.html.twig', [
            'van' => $van,
        ]);
    }
}
