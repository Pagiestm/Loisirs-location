<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EmailVerificationController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
        private readonly MailService $mailService,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {}

    /**
     * Vérifie l'email d'un utilisateur via le token
     */
    #[Route('/verify-email/{token}', name: 'app_verify_email', methods: ['GET'])]
    public function verifyEmail(string $token): Response
    {
        $user = $this->userRepository->findOneBy(['verificationToken' => $token]);

        if (!$user) {
            $this->addFlash('error', 'Le lien de vérification est invalide.');
            return $this->redirectToRoute('app_home');
        }

        if ($user->isVerified()) {
            $this->addFlash('info', 'Votre email est déjà vérifié. Vous pouvez vous connecter.');
            return $this->redirectToRoute('app_home');
        }

        if ($user->isVerificationTokenExpired()) {
            $this->addFlash('error', 'Le lien de vérification a expiré. Un nouveau lien vous a été envoyé par email.');
            $this->sendVerificationEmail($user);
            return $this->redirectToRoute('app_home');
        }

        // Activer le compte
        $user->setIsVerified(true);
        $user->setVerificationToken(null);
        $user->setVerificationTokenExpiresAt(null);

        $this->entityManager->flush();

        $this->addFlash('success', 'Votre email a été vérifié avec succès ! Vous pouvez maintenant vous connecter.');

        return $this->redirectToRoute('app_home');
    }

    /**
     * Renvoie un email de vérification
     */
    #[Route('/resend-verification-email', name: 'app_resend_verification_email', methods: ['POST'])]
    public function resendVerificationEmail(Request $request): Response
    {
        $email = $request->request->get('email');

        if (!$email) {
            $this->addFlash('error', 'Veuillez fournir une adresse email.');
            return $this->redirectToRoute('app_home');
        }

        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            // Pour des raisons de sécurité, on ne révèle pas si l'email existe
            $this->addFlash('success', 'Si un compte existe avec cet email, un nouveau lien de vérification a été envoyé.');
            return $this->redirectToRoute('app_home');
        }

        if ($user->isVerified()) {
            $this->addFlash('info', 'Votre email est déjà vérifié. Vous pouvez vous connecter.');
            return $this->redirectToRoute('app_home');
        }

        $this->sendVerificationEmail($user);

        $this->addFlash('success', 'Un nouveau lien de vérification a été envoyé à votre adresse email.');

        return $this->redirectToRoute('app_home');
    }

    /**
     * Génère et envoie un email de vérification
     */
    public function sendVerificationEmail(User $user): bool
    {
        if ($user->isVerified()) {
            return false;
        }

        if ($user->getVerificationTokenExpiresAt() && $user->getVerificationTokenExpiresAt() > new \DateTimeImmutable()) {
            return false;
        }

        // Générer un nouveau token
        $token = bin2hex(random_bytes(32));
        $user->setVerificationToken($token);

        // Le token expire dans 24 heures
        $expiresAt = new \DateTimeImmutable('+24 hours');
        $user->setVerificationTokenExpiresAt($expiresAt);

        $this->entityManager->flush();

        // Générer l'URL de vérification
        $verificationUrl = $this->urlGenerator->generate(
            'app_verify_email',
            ['token' => $token],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        // Envoyer l'email
        $this->mailService->sendMjmlEmail(
            to: $user->getEmail(),
            subject: 'Vérification de votre email - Loisirs Location',
            mjmlTemplate: 'email/email_verification.mjml.twig',
            context: [
                'firstName' => $user->getFirstName(),
                'verificationUrl' => $verificationUrl,
            ]
        );

        return true;
    }
}
