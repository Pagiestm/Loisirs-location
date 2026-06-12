<?php

namespace App\Controller;

use App\Interface\EditableControllerInterface;
use App\Repository\NewsletterRepository;
use App\Repository\Van\VanRepository;
use App\Service\GoogleReviewsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController implements EditableControllerInterface
{
    #[Route('/', name: 'app_home')]
    public function index(GoogleReviewsService $googleReviews, VanRepository $vanRepository): Response
    {
        return $this->render('pages/home/index.html.twig', [
            'placeData' => $googleReviews->getPlaceData(5),
            'vans'      => $vanRepository->findAllWithEquipments(),
        ]);
    }

    #[Route('/mentions-legales', name: 'app_legal_mentions')]
    public function legalMentions(): Response
    {
        return $this->render('pages/home/legal-mentions.html.twig');
    }

    #[Route('/politique-de-confidentialite', name: 'app_privacy_policy')]
    public function privacyPolicy(): Response
    {
        return $this->render('pages/home/privacy-policy.html.twig');
    }

    #[Route('/newsletter/desinscription', name: 'app_newsletter_unsubscribe')]
    public function unsubscribeNewsletter(
        #[MapQueryParameter] string $email,
        #[MapQueryParameter] string $token,
        NewsletterRepository $newsletterRepository,
        EntityManagerInterface $em,
    ): Response {
        $newsletterSecret = $this->getParameter('newsletter_secret');
        $expectedToken = hash_hmac('sha256', $email, $newsletterSecret);

        if (!hash_equals($expectedToken, $token)) {
            throw $this->createNotFoundException('Lien invalide.');
        }

        $newsletter = $newsletterRepository->find($email);

        if ($newsletter) {
            $em->remove($newsletter);
            $em->flush();
        }

        $this->addFlash('success', 'Vous avez été désinscrit(e) de notre newsletter.');

        return $this->redirectToRoute('app_home');
    }
}
