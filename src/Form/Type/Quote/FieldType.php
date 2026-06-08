<?php

namespace App\Form\Type\Quote;

use App\Dto\Quote\ChoiceOption;
use App\Entity\Quote\Field;
use App\Enum\FieldEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class FieldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            /** @var Field|null $data */
            $data = $event->getData();

            $choicesData = [];
            if ($data) {
                // On utilise en priorité choices_list s'il existe pour conserver l'ordre
                if (isset($data->getOptions()['choices_list'])) {
                    foreach ($data->getOptions()['choices_list'] as $choice) {
                        $dto = new ChoiceOption();
                        $dto->label = $choice['label'] ?? null;
                        $dto->value = $choice['value'] ?? null;
                        $choicesData[] = $dto;
                    }
                } elseif (isset($data->getOptions()['choices'])) {
                    foreach ($data->getOptions()['choices'] as $choiceLabel => $choiceVal) {
                        $dto = new ChoiceOption();
                        $dto->label = $choiceLabel;
                        $dto->value = $choiceVal;
                        $choicesData[] = $dto;
                    }
                }
            }

            $form
                ->add('label', TextType::class, [
                    'required' => true,
                    'label' => 'Libellé du champ',
                    'mapped' => false,
                    'data' => $data ? $data->getOptions()['label'] ?? null : null,
                    'label_attr' => ['class' => 'required'],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Le libellé du champ est obligatoire.',
                        ]),
                    ],
                ])
                ->add('type', EnumType::class, [
                    'required' => true,
                    'label' => 'Type de champ',
                    'class' => FieldEnum::class,
                    'label_attr' => ['class' => 'required'],
                    'choice_label' => function (?FieldEnum $choice) {
                        return $choice?->label();
                    },
                    'attr' => [
                        'data-field-type-target' => 'type',
                        'data-action' => 'change->field-type#toggleChoices',
                    ],
                ])
                ->add('choices', CollectionType::class, [
                    'required' => false,
                    'mapped' => false,
                    'label' => 'Options de configuration',
                    'entry_type' => ChoiceOptionType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'data' => $choicesData,
                    'row_attr' => [
                        'data-field-type-target' => 'choicesContainer',
                        'class' => 'd-none',
                    ],
                ])
                ->add('watermark', ChoiceType::class, [
                    'required' => true,
                    'mapped' => false,
                    'label' => 'Ajouter un filigrane',
                    'choices' => [
                        'Oui' => true,
                        'Non' => false,
                    ],
                    'expanded' => true,
                    'multiple' => false,
                    'row_attr' => [
                        'data-field-type-target' => 'watermarkContainer',
                        'class' => 'd-none',
                    ],
                    'data' => $data ? ($data->getOptions()['attr']['watermark'] ?? false) : false,
                ])
                ->add('required', ChoiceType::class, [
                    'required' => true,
                    'mapped' => false,
                    'label' => 'Obligatoire',
                    'expanded' => true,
                    'multiple' => false,
                    'data' => $data ? ($data->getOptions()['required'] ?? true) : true,
                    'empty_data' => false,
                    'choices' => [
                        'Oui' => true,
                        'Non' => false,
                    ],
                    'constraints' => [
                        new NotNull([
                            'message' => 'Veuillez indiquer si le champ est obligatoire ou non.',
                        ]),
                    ],
                ])
                ->add('position', IntegerType::class, [
                    'required' => false,
                    'label' => 'Position',
                ])
            ;
        });

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            /** @var Field $field */
            $field = $form->getData();
            $options = [
                'mapped' => false,
            ];

            if (null !== ($label = $form->get('label')->getData())) {
                $options['label'] = $label;
            }

            if (null !== ($required = $form->get('required')->getData())) {
                $options['required'] = $required;
            }

            $type = $form->get('type')->getData();
            if ($type && in_array($type->value, ['select', 'checkbox', 'radio'])) {
                $choicesData = $form->get('choices')->getData();
                if (is_array($choicesData)) {
                    $choicesList = [];
                    foreach ($choicesData as $choiceDto) {
                        $choiceLabel = $choiceDto->label ?? null;
                        if (!empty($choiceLabel)) {
                            $choiceValue = !empty($choiceDto->value) ? $choiceDto->value : $choiceLabel;
                            // On stocke sous forme de liste pour que la base de données (JSON) conserve l'ordre !
                            $choicesList[] = ['label' => $choiceLabel, 'value' => $choiceValue];
                        }
                    }
                    if (!empty($choicesList)) {
                        $options['choices_list'] = $choicesList;
                    }
                }

                if ($type->value === 'checkbox') {
                    $options['expanded'] = true;
                    $options['multiple'] = true;
                } elseif ($type->value === 'radio') {
                    $options['expanded'] = true;
                    $options['multiple'] = false;
                } else {
                    $options['expanded'] = false;
                    $options['multiple'] = false;
                }
            } else if ($type && $type->value === 'file') {
                $options['attr']['accept'] = '.pdf, .jpg, .jpeg, .png';
                $options['attr']['watermark'] = $form->get('watermark')->getData();
            }


            $field->setOptions($options);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Field::class,
            'attr' => [
                'data-controller' => 'field-type'
            ],
        ]);
    }
}
