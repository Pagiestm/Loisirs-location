<?php

namespace App\Controller\Admin\Quote;

use App\Entity\Quote\Quote;
use App\Form\Type\Quote\FieldType;
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

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name', 'Titre du devis');

        yield TextEditorField::new('description', 'Description du devis');

        yield CollectionField::new('fields', 'Champs')
            ->setEntryType(FieldType::class);
    }
}
