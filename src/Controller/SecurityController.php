<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RequestPasswordResetType;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SecurityController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
        private readonly MailService $mailService,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {}

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    // ========== EMAIL VERIFICATION ==========

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

    // ========== PASSWORD RESET ==========

    /**
     * Demande de réinitialisation du mot de passe
     */
    #[Route('/forgot-password', name: 'app_forgot_password', methods: ['GET', 'POST'])]
    public function forgotPassword(Request $request): Response
    {
        $form = $this->createForm(RequestPasswordResetType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $user = $this->userRepository->findOneBy(['email' => $email]);

            // Pour des raisons de sécurité, on ne révèle pas si l'email existe
            if ($user && $user->isVerified()) {
                $this->sendPasswordResetEmail($user);
            }

            $this->addFlash('success', 'Si un compte existe avec cet email, vous recevrez un lien de réinitialisation dans quelques instants.');
            return $this->redirectToRoute('app_home');
        }

        $response = new Response();
        if ($form->isSubmitted() && !$form->isValid()) {
            $response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->render('pages/security/forgot_password.html.twig', [
            'form' => $form->createView(),
        ], $response);
    }

    /**
     * Réinitialisation du mot de passe via le token
     */
    #[Route('/reset-password/{token}', name: 'app_reset_password', methods: ['GET', 'POST'])]
    public function resetPassword(string $token, Request $request): Response
    {
        $user = $this->userRepository->findOneBy(['resetPasswordToken' => $token]);

        if (!$user) {
            $this->addFlash('error', 'Le lien de réinitialisation est invalide.');
            return $this->redirectToRoute('app_home');
        }

        if ($user->isResetPasswordTokenExpired()) {
            $this->addFlash('error', 'Le lien de réinitialisation a expiré. Veuillez faire une nouvelle demande.');
            return $this->redirectToRoute('app_forgot_password');
        }

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hasher le nouveau mot de passe
            $plainPassword = $form->get('plainPassword')->getData();
            $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);

            // Supprimer le token
            $user->setResetPasswordToken(null);
            $user->setResetPasswordTokenExpiresAt(null);

            // Mettre à jour updatedAt
            $user->setUpdatedAt(new \DateTimeImmutable());

            $this->entityManager->flush();

            $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès ! Vous pouvez maintenant vous connecter.');
            return $this->redirectToRoute('app_home');
        }

        $response = new Response();
        if ($form->isSubmitted() && !$form->isValid()) {
            $response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->render('pages/security/reset_password.html.twig', [
            'form' => $form->createView(),
        ], $response);
    }

    /**
     * Génère et envoie un email de réinitialisation de mot de passe
     */
    private function sendPasswordResetEmail(User $user): bool
    {
        // Générer un nouveau token
        $token = bin2hex(random_bytes(32));
        $user->setResetPasswordToken($token);

        // Le token expire dans 1 heure
        $expiresAt = new \DateTimeImmutable('+1 hour');
        $user->setResetPasswordTokenExpiresAt($expiresAt);

        $this->entityManager->flush();

        // Générer l'URL de réinitialisation
        $resetUrl = $this->urlGenerator->generate(
            'app_reset_password',
            ['token' => $token],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        // Envoyer l'email
        $this->mailService->sendMjmlEmail(
            to: $user->getEmail(),
            subject: 'Réinitialisation de votre mot de passe - Loisirs Location',
            mjmlTemplate: 'email/password_reset.mjml.twig',
            context: [
                'firstName' => $user->getFirstName(),
                'resetUrl' => $resetUrl,
                'expiresAt' => $expiresAt,
            ]
        );

        return true;
    }
}
