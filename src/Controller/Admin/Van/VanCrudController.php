<?php

namespace App\Controller\Admin\Van;

use App\Entity\Van\Van;
use App\Form\Type\Van\VanImageType;
use App\Form\Type\Van\VanOptionType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class VanCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Van::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name', 'Nom')
            ->hideOnForm();

        yield TextField::new('subtitle', 'Sous-titre')
            ->hideOnForm();

        yield FormField::addTab('Informations', 'fa fa-van-shuttle')->onlyOnForms();
        yield FormField::addFieldset('Présentation', 'fa fa-align-left')->onlyOnForms();
        yield FormField::addRow('md')->onlyOnForms();

        yield TextField::new('name', 'Nom')
            ->setColumns('col-md-6')
            ->onlyOnForms();

        yield TextField::new('subtitle', 'Sous-titre')
            ->setColumns('col-md-6')
            ->onlyOnForms();

        yield TextareaField::new('description', 'Description')
            ->setColumns('col-12')
            ->setNumOfRows(5)
            ->onlyOnForms();

        yield FormField::addTab('Configuration', 'fa fa-sliders')->onlyOnForms();
        yield FormField::addFieldset('Équipements', 'fa fa-list-check')->onlyOnForms();
        yield CollectionField::new('options', 'Options')
            ->setEntryType(VanOptionType::class)
            ->setFormTypeOption('by_reference', false)
            ->setColumns('col-12')
            ->onlyOnForms();

        yield FormField::addFieldset('Galerie', 'fa fa-images')->onlyOnForms();
        yield CollectionField::new('images', 'Images')
            ->setEntryType(VanImageType::class)
            ->setFormTypeOption('by_reference', false)
            ->setColumns('col-12')
            ->onlyOnForms();
    }
}
