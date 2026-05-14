<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'placeholder' => 'Jean',
                    'autocomplete' => 'given-name',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Le prénom est requis'),
                    new Assert\Length(max: 50),
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Dupont',
                    'autocomplete' => 'family-name',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Le nom est requis'),
                    new Assert\Length(max: 50),
                ],
            ])
            ->add('phone', TelType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'attr' => [
                    'placeholder' => '06 12 34 56 78',
                    'autocomplete' => 'tel',
                ],
                'constraints' => [
                    new Assert\Length(max: 20),
                    new Assert\Regex(
                        pattern: '/^$|^[+0-9][0-9\s().-]{7,19}$/',
                        message: 'Veuillez saisir un numero de telephone valide.',
                    ),
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
