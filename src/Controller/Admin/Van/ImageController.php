<?php

namespace App\Controller\Admin\Van;

use App\Repository\Van\VanImageRepository;
use App\Repository\Van\VanRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ImageController extends AbstractController
{
    #[AdminRoute('/vans/gallery', name: 'vans_gallery')]
    public function gallery(VanRepository $vanRepository): Response
    {
        $vans = $vanRepository->findAllWithImages();

        return $this->render('admin/van/gallery.html.twig', [
            'vans' => $vans,
        ]);
    }

    #[AdminRoute('/van/image/{entityId}/delete', name: 'van_image_delete')]
    public function deleteImage(int $entityId, VanImageRepository $vanImageRepository, EntityManagerInterface $em): Response
    {
        $vanImage = $vanImageRepository->find($entityId);

        if (!$vanImage) {
            throw $this->createNotFoundException('Image introuvable.');
        }

        $em->remove($vanImage);
        $em->flush();

        return $this->redirectToRoute('admin_vans_gallery');
    }
}
