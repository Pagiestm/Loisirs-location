<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prenom',
                'attr' => [
                    'placeholder' => 'Votre prenom',
                    'autocomplete' => 'given-name',
                    'maxlength' => 80,
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Veuillez saisir votre prenom.'),
                    new Assert\Length(
                        min: 2,
                        max: 80,
                        minMessage: 'Le prenom doit contenir au moins {{ limit }} caracteres.',
                        maxMessage: 'Le prenom ne peut pas depasser {{ limit }} caracteres.'
                    ),
                    new Assert\Regex(
                        pattern: "/^[\\p{L}\\s\-'`]+$/u",
                        message: 'Le prenom contient des caracteres non autorises.'
                    ),
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Votre nom',
                    'autocomplete' => 'family-name',
                    'maxlength' => 80,
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Veuillez saisir votre nom.'),
                    new Assert\Length(
                        min: 2,
                        max: 80,
                        minMessage: 'Le nom doit contenir au moins {{ limit }} caracteres.',
                        maxMessage: 'Le nom ne peut pas depasser {{ limit }} caracteres.'
                    ),
                    new Assert\Regex(
                        pattern: "/^[\\p{L}\\s\-'`]+$/u",
                        message: 'Le nom contient des caracteres non autorises.'
                    ),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
                'attr' => [
                    'placeholder' => 'votre-email@exemple.com',
                    'autocomplete' => 'email',
                    'maxlength' => 180,
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Veuillez saisir votre adresse email.'),
                    new Assert\Length(
                        max: 180,
                        maxMessage: 'L\'adresse email ne peut pas depasser {{ limit }} caracteres.'
                    ),
                    new Assert\Email(message: 'Veuillez saisir une adresse email valide.'),
                ],
            ])
            ->add('phone', TextType::class, [
                'required' => false,
                'label' => 'Telephone',
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
            ->add('subject', TextType::class, [
                'label' => 'Sujet',
                'attr' => [
                    'placeholder' => 'Ex: Demande de devis pour un week-end',
                    'maxlength' => 120,
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Veuillez saisir un sujet.'),
                    new Assert\Length(
                        min: 5,
                        max: 120,
                        minMessage: 'Le sujet doit contenir au moins {{ limit }} caracteres.',
                        maxMessage: 'Le sujet ne peut pas depasser {{ limit }} caracteres.'
                    ),
                ],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Votre message',
                'attr' => [
                    'rows' => 7,
                    'placeholder' => 'Decrivez votre besoin (dates, destination, nombre de personnes, etc.)',
                    'minlength' => 20,
                    'maxlength' => 4000,
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Veuillez saisir votre message.'),
                    new Assert\Length(
                        min: 20,
                        max: 4000,
                        minMessage: 'Votre message doit contenir au moins {{ limit }} caracteres.',
                        maxMessage: 'Votre message ne peut pas depasser {{ limit }} caracteres.'
                    ),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
