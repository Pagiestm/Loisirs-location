<?php

namespace App\EasyAdmin\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class LucideIconField implements FieldInterface
{
    use FieldTrait;

    public static function new(string $propertyName, ?string $label = null): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label ?? 'Icone')
            ->setTemplatePath('admin/field/lucide_icon.html.twig')
            ->setFormType(TextType::class)
            ->setHelp('Choisissez une icône visuellement dans la galerie.')
            ->setFormTypeOption('attr.placeholder', 'Rechercher une icône')
            ->setFormTypeOption('attr.autocomplete', 'off')
            ->setFormTypeOption('attr.data-controller', 'lucide-icon-picker');
    }
}
