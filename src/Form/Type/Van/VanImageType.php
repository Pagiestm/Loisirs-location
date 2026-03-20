<?php

namespace App\Form\Type\Van;

use App\Entity\Van\VanImage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class VanImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'download_uri' => false,
                'label' => 'Image',
            ])
            ->add('caption', TextType::class, [
                'required' => false,
                'label' => 'Legende',
            ])
            ->add('position', IntegerType::class, [
                'required' => false,
                'label' => 'Position',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => VanImage::class,
        ]);
    }
}
