<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints as Assert;

class EmailChangeRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('newEmail', EmailType::class, [
                'label' => 'Nouvelle adresse email',
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'nouvel-email@exemple.com',
                    'autocomplete' => 'email',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'La nouvelle adresse email est requise.'),
                    new Assert\Email(message: 'Cette adresse email n\'est pas valide.'),
                    new Assert\Length(max: 100),
                ],
            ])
            ->add('currentPassword', PasswordType::class, [
                'label' => 'Mot de passe actuel',
                'mapped' => false,
                'attr' => [
                    'placeholder' => '••••••••',
                    'autocomplete' => 'current-password',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Le mot de passe actuel est requis.'),
                    new UserPassword(message: 'Le mot de passe actuel est incorrect.'),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
