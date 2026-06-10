<?php

namespace App\Form\Type\Van;

use App\Entity\Van\Equipment;
use App\Entity\Van\VanEquipment;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VanEquipmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('equipment', EntityType::class, [
                'required' => false,
                'label' => 'Équipement',
                'class' => Equipment::class,
                'help' => 'Lier un équipement existant ou créer un nouveau équipement en saisissant son nom.',
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
            'data_class' => VanEquipment::class,
        ]);
    }
}
