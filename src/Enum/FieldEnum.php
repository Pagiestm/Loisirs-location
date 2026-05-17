<?php

namespace App\Enum;

enum FieldEnum: string
{
    case TEXT = 'text';
    case EMAIL = 'email';
    case TEXTAREA = 'textarea';
    case SELECT = 'select';
    case CHECKBOX = 'checkbox';
    case RADIO = 'radio';

    public function label(): string
    {
        return match ($this) {
            self::TEXT => 'Réponse courte',
            self::EMAIL => 'Adresse e-mail',
            self::TEXTAREA => 'Réponse longue',
            self::SELECT => 'Liste déroulante',
            self::CHECKBOX => 'Cases à cocher',
            self::RADIO => 'Choix multiples',
        };
    }

    public function formType(): string
    {
        return match ($this) {
            self::TEXT => 'Symfony\Component\Form\Extension\Core\Type\TextType',
            self::EMAIL => 'Symfony\Component\Form\Extension\Core\Type\EmailType',
            self::TEXTAREA => 'Symfony\Component\Form\Extension\Core\Type\TextareaType',
            self::SELECT => 'Symfony\Component\Form\Extension\Core\Type\ChoiceType',
            self::CHECKBOX => 'Symfony\Component\Form\Extension\Core\Type\ChoiceType',
            self::RADIO => 'Symfony\Component\Form\Extension\Core\Type\ChoiceType',
        };
    }
}
