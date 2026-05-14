<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EmailChangeRequestType;
use App\Form\ProfileType;
use App\Repository\UserRepository;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly MailService $mailService,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {}

    #[Route('/profile', name: 'app_profile', methods: ['GET','POST'])]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUpdatedAt(new \DateTimeImmutable());
            $em->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès.');

            return $this->redirectToRoute('app_profile');
        }

        $emailChangeForm = $this->createForm(EmailChangeRequestType::class);

        $response = new Response();
        if ($form->isSubmitted() && !$form->isValid()) {
            $response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->render('pages/profile/index.html.twig', [
            'form' => $form->createView(),
            'emailChangeForm' => $emailChangeForm->createView(),
        ], $response);
    }

    #[Route('/profile/request-email-change', name: 'app_profile_request_email_change', methods: ['GET','POST'])]
    public function requestEmailChange(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $emailChangeForm = $this->createForm(EmailChangeRequestType::class);
        $emailChangeForm->handleRequest($request);

        $form = $this->createForm(ProfileType::class, $user);

        if ($emailChangeForm->isSubmitted() && $emailChangeForm->isValid()) {
            $newEmail = strtolower(trim((string) $emailChangeForm->get('newEmail')->getData()));

            if ($newEmail === strtolower((string) $user->getEmail())) {
                $this->addFlash('error', 'Cette adresse email est deja utilisee par votre compte.');

                return $this->redirectToRoute('app_profile');
            }

            $existing = $this->userRepository->findOneBy(['email' => $newEmail]);
            if ($existing !== null) {
                $this->addFlash('error', 'Cette adresse email est deja utilisee.');

                return $this->redirectToRoute('app_profile');
            }

            $token = bin2hex(random_bytes(32));
            $expiresAt = new \DateTimeImmutable('+1 hour');
            $oldEmail = (string) $user->getEmail();

            $user
                ->setPendingEmail($newEmail)
                ->setEmailChangeToken($token)
                ->setEmailChangeTokenExpiresAt($expiresAt)
                ->setUpdatedAt(new \DateTimeImmutable())
            ;

            $em->flush();

            $confirmUrl = $this->urlGenerator->generate(
                'app_profile_confirm_email_change',
                ['token' => $token],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $this->mailService->sendMjmlEmail(
                to: $newEmail,
                subject: 'Confirmez votre nouvelle adresse email - Loisirs Location',
                mjmlTemplate: 'email/email_change_confirmation.mjml.twig',
                context: [
                    'firstName' => $user->getFirstName(),
                    'newEmail' => $newEmail,
                    'oldEmail' => $oldEmail,
                    'confirmUrl' => $confirmUrl,
                    'expiresAt' => $expiresAt,
                ]
            );

            $this->mailService->sendMjmlEmail(
                to: $oldEmail,
                subject: 'Demande de changement d\'adresse email - Loisirs Location',
                mjmlTemplate: 'email/email_change_notice.mjml.twig',
                context: [
                    'firstName' => $user->getFirstName(),
                    'newEmail' => $newEmail,
                    'oldEmail' => $oldEmail,
                    'supportEmail' => 'contact@loisirs-location.fr',
                ]
            );

            $this->addFlash('success', 'Un email de confirmation a ete envoye a votre nouvelle adresse.');

            return $this->redirectToRoute('app_profile');
        }

        $response = new Response();
        if (($form->isSubmitted() && !$form->isValid()) || ($emailChangeForm->isSubmitted() && !$emailChangeForm->isValid())) {
            $response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->render('pages/profile/index.html.twig', [
            'form' => $form->createView(),
            'emailChangeForm' => $emailChangeForm->createView(),
        ], $response);
    }

    #[Route('/profile/confirm-email-change/{token}', name: 'app_profile_confirm_email_change', methods: ['GET'])]
    public function confirmEmailChange(string $token, EntityManagerInterface $em): Response
    {
        $user = $this->userRepository->findOneBy(['emailChangeToken' => $token]);

        if (!$user instanceof User || !$user->getPendingEmail()) {
            $this->addFlash('error', 'Le lien de confirmation est invalide.');

            return $this->redirectToRoute('app_home');
        }

        if ($user->isEmailChangeTokenExpired()) {
            $user->clearEmailChangeRequest()->setUpdatedAt(new \DateTimeImmutable());
            $em->flush();

            $this->addFlash('error', 'Le lien de confirmation a expire. Merci de refaire la demande depuis votre profil.');

            return $this->redirectToRoute('app_profile');
        }

        $existing = $this->userRepository->findOneBy(['email' => $user->getPendingEmail()]);
        if ($existing !== null && $existing->getId() !== $user->getId()) {
            $user->clearEmailChangeRequest()->setUpdatedAt(new \DateTimeImmutable());
            $em->flush();

            $this->addFlash('error', 'Cette adresse email est deja utilisee. Merci de recommencer la procedure.');

            return $this->redirectToRoute('app_profile');
        }

        $newEmail = (string) $user->getPendingEmail();

        $user
            ->setEmail($newEmail)
            ->clearEmailChangeRequest()
            ->setUpdatedAt(new \DateTimeImmutable())
        ;

        $em->flush();

        $this->addFlash('success', 'Votre adresse email a bien ete mise a jour.');

        return $this->redirectToRoute('app_profile');
    }

    #[Route('/profile/delete', name: 'app_profile_delete', methods: ['POST'])]
    public function deleteAccount(
        Request $request,
        EntityManagerInterface $em,
        TokenStorageInterface $tokenStorage,
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$this->isCsrfTokenValid('delete_account', (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Action invalide. Merci de reessayer.');

            return $this->redirectToRoute('app_profile');
        }

        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $em->remove($user);
        $em->flush();

        $tokenStorage->setToken(null);
        $request->getSession()?->invalidate();

        return $this->redirectToRoute('app_home');
    }
}
