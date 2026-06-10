<?php

namespace App\Controller\Admin\Quote;

use App\Entity\Quote\Quote;
use App\Form\Type\Quote\FieldType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class QuoteCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Quote::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Devis')
            ->setEntityLabelInPlural('Devis')
            ->setSearchFields(['name', 'description']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name', 'Titre du devis');

        yield TextEditorField::new('description', 'Description du devis')->formatValue(function ($value, $entity) {
            return nl2br($value);
        });

        yield CollectionField::new('fields', 'Champs')
            ->setEntryType(FieldType::class)
            ->hideOnIndex();
    }
}
