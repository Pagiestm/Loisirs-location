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
            ->setHelp('Entrez le nom de l\'icône Lucide (ex: "user", "camera", "heart", etc.). Vous pouvez consulter la liste complète des icônes sur <a href="https://lucide.dev/icons/" target="_blank" class="underline">le site de Lucide</a>.')
            ->setFormTypeOption('attr.placeholder', 'user-circle')
            ->setFormTypeOption('attr.autocomplete', 'off');
    }
}
