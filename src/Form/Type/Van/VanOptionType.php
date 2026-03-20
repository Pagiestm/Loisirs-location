<?php

namespace App\Form\Type\Van;

use App\Entity\Van\Option;
use App\Entity\Van\VanOption;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VanOptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('option', EntityType::class, [
                'required' => false,
                'label' => 'Option',
                'class' => Option::class,
                'help' => 'Lier une option existante ou créer une nouvelle option en saisissant son nom.',
                'choice_label' => 'name',
            ])
            ->add('value', TextType::class, [
                'required' => false,
                'label' => 'Valeur',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => VanOption::class,
        ]);
    }
}
