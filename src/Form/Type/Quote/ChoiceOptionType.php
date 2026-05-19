<?php

namespace App\Form\Type\Quote;

use App\Dto\Quote\ChoiceOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChoiceOptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label', TextType::class, [
                'label' => 'Libellé affiché',
                'required' => true,
            ])
            ->add('value', TextType::class, [
                'label' => 'Valeur (optionnelle, sinon = libellé)',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ChoiceOption::class,
        ]);
    }
}
