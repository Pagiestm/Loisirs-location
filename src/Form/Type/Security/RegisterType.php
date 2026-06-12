<?php

namespace App\Form\Type\Security;

use App\Entity\User;
use App\Validator\Constraints\StrongPassword;
use App\Validator\Constraints\UniqueEmail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'placeholder' => 'Jean',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Le prénom est requis'),
                    new Assert\Length(max: 50, maxMessage: 'Le prénom ne peut pas dépasser {{ limit }} caractères'),
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Dupont',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Le nom est requis'),
                    new Assert\Length(max: 50, maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères'),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'placeholder' => 'votre@email.com',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'L\'email est requis'),
                    new Assert\Email(message: 'L\'email n\'est pas valide'),
                    new UniqueEmail(),
                ],
            ])
            ->add('phone', TelType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'attr' => [
                    'placeholder' => '06 12 34 56 78',
                    'autocomplete' => 'tel',
                    'maxlength' => 20,
                ],
                'constraints' => [
                    new Assert\Length(
                        max: 20,
                        maxMessage: 'Le numero de telephone ne peut pas depasser {{ limit }} caracteres.'
                    ),
                    new Assert\Regex(
                        pattern: '/^$|^[+0-9][0-9\s().-]{7,19}$/',
                        message: 'Veuillez saisir un numero de telephone valide.'
                    ),
                ],
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'always_empty' => false,
                'attr' => [
                    'placeholder' => '••••••••',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Le mot de passe est requis'),
                    new StrongPassword(),
                ],
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'mapped' => false,
                'attr' => [
                    'placeholder' => '123 rue de la Paix',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'L\'adresse est requise'),
                ],
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Code postal',
                'mapped' => false,
                'attr' => [
                    'placeholder' => '75001',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Le code postal est requis'),
                    new Assert\Length(max: 10),
                ],
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'Paris',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'La ville est requise'),
                ],
            ])
            ->add('complement', TextType::class, [
                'label' => 'Complément d\'adresse',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Appartement, bâtiment...',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
