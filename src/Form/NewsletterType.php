<?php

namespace App\Form;

use App\Entity\Newsletter;
use App\Validator\Constraints\UniqueEmail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class NewsletterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'S’inscrire à notre newsletter',
                'attr' => [
                    'placeholder' => 'Votre e-mail',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'L\'email est requis'),
                    new Assert\Email(message: 'L\'email n\'est pas valide'),
                    new UniqueEmail(repo: 'newsletter', message: 'Cet email est déjà inscrit à la newsletter'),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Newsletter::class,
        ]);
    }
}
