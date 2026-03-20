<?php

namespace App\Controller;

use App\Interface\EditableControllerInterface;
use App\Repository\Van\VanRepository;
use App\Service\GoogleReviewsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController implements EditableControllerInterface
{
    #[Route('/', name: 'app_home')]
    public function index(GoogleReviewsService $googleReviews, VanRepository $vanRepository): Response
    {
        return $this->render('pages/home/index.html.twig', [
            'placeData' => $googleReviews->getPlaceData(5),
            'vans'      => $vanRepository->findAllWithOptions(),
        ]);
    }
}
