<?php

namespace App\Twig\Components\Security;

use App\Controller\SecurityController;
use App\Form\Type\Security\LoginType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/security/Login.html.twig')]
final class Login extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;

    #[LiveProp]
    public ?array $initialFormData = null;

    #[LiveProp]
    public ?string $error = null;

    #[LiveProp]
    public ?string $success = null;

    public function __construct(
        private readonly AuthenticationUtils $authenticationUtils,
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly SecurityController $securityController,
    ) {}

    public function mount(): void
    {
        // Récupérer le dernier email utilisé et l'erreur d'authentification
        if ($this->initialFormData === null) {
            $lastUsername = $this->authenticationUtils->getLastUsername();
            $this->initialFormData = [
                'email' => $lastUsername,
            ];
        }

        $authError = $this->authenticationUtils->getLastAuthenticationError();
        if ($authError) {
            $this->error = 'Email ou mot de passe incorrect.';
        }
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(LoginType::class, $this->initialFormData);
    }

    #[LiveListener('registrationSuccess')]
    public function onRegistrationSuccess(): void
    {
        $this->success = 'Pour finaliser votre inscription, veuillez cliquer sur le lien de confirmation envoyé à votre adresse email.';
    }

    #[LiveAction]
    public function submitLogin(Security $security): void
    {
        $this->success = null;

        // Soumettre et valider le formulaire
        $this->submitForm();

        // Récupérer le formulaire
        $form = $this->getForm();

        // Si le formulaire n'est pas valide, arrêter ici
        if (!$form->isValid()) {
            $this->error = 'Veuillez corriger les erreurs du formulaire.';
            return;
        }

        // Récupérer les données du formulaire
        $data = $form->getData();
        $email = $data['email'];
        $password = $data['password'];

        // Vérifier que l'utilisateur existe
        $user = $this->userRepository->findOneBy(['email' => $email]);
        if (!$user) {
            $this->error = 'Email ou mot de passe incorrect.';
            return;
        }

        // Vérifier le mot de passe
        if (!$this->passwordHasher->isPasswordValid($user, $password)) {
            $this->error = 'Email ou mot de passe incorrect.';
            return;
        }

        try {
            // Tenter l'authentification
            $security->login($user);

            // Marquer le succès et dispatcher l'événement de reload
            $this->error = null;

            $this->dispatchBrowserEvent('window:reload');
        } catch (CustomUserMessageAccountStatusException $e) {
            // Cas spécial : compte non vérifié
            // Renvoyer un email de vérification
            if ($this->securityController->sendVerificationEmail($user)) {
                $this->error = 'Votre compte n\'est pas encore activé. Un nouvel email de vérification vient de vous être envoyé. Veuillez consulter votre boîte de réception.';
            } else {
                $this->error = 'Votre compte n\'est pas encore activé. Veuillez vérifier votre boîte de réception pour activer votre compte.';
            }
        } catch (AuthenticationException $e) {
            // En cas d'échec, afficher l'erreur
            $this->error = 'Email ou mot de passe incorrect.';
        }
    }
}
