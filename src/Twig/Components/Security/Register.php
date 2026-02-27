<?php

namespace App\Twig\Components\Security;

use App\Controller\SecurityController;
use App\Entity\Address;
use App\Entity\User;
use App\Form\Type\Security\RegisterType;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/security/Register.html.twig')]
class Register extends AbstractController
{
    use DefaultActionTrait;
    use ComponentToolsTrait;
    use ComponentWithFormTrait;

    #[LiveProp]
    public ?User $initialFormData = null;

    public ?string $error = null;
    public ?string $success = null;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly MailService $mailService,
        private readonly SecurityController $securityController
    ) {}

    #[LiveAction]
    public function submitRegister(): void
    {
        // Soumettre le formulaire - cela valide et hydrate l'objet
        $this->submitForm();

        $form = $this->getForm();

        // Si le formulaire n'est pas valide, arrêter ici
        // Les erreurs seront affichées automatiquement
        if (!$form->isValid()) {
            return;
        }

        /** @var User $user */
        $user = $form->getData();

        // Vérifier si l'email existe déjà
        $existingUser = $this->entityManager->getRepository(User::class)
            ->findOneBy(['email' => $user->getEmail()]);

        if ($existingUser) {
            $this->error = 'Un compte existe déjà avec cet email.';
            return;
        }

        // Créer l'adresse depuis les données du formulaire
        $address = new Address();
        $address->setAddress($form->get('address')->getData());
        $address->setCity($form->get('city')->getData());
        $address->setPostalCode($form->get('postalCode')->getData());
        $complement = $form->get('complement')->getData();
        if ($complement) {
            $address->setComplement($complement);
        }

        $user->setAddress($address);

        // Hasher le mot de passe
        $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
        $user->setPassword($hashedPassword);

        // L'utilisateur n'est pas vérifié par défaut
        $user->setIsVerified(false);

        try {
            $this->entityManager->persist($address);
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // Envoyer l'email de vérification
            $this->securityController->sendVerificationEmail($user);

            $this->success = 'Votre compte a été créé avec succès ! Un email de vérification vous a été envoyé. Veuillez vérifier votre boîte de réception pour activer votre compte.';

            // Réinitialiser le formulaire
            $this->resetForm();
            $this->dispatchBrowserEvent('registration:success');
        } catch (\Exception $e) {
            $this->error = 'Une erreur est survenue lors de la création de votre compte : ' . $e->getMessage();
        }
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(RegisterType::class, $this->initialFormData);
    }
}
