<?php

namespace App\Controller\Admin\Van;

use App\EasyAdmin\Field\LucideIconField;
use App\Entity\Van\Option;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class OptionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Option::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name', 'Nom');
        yield LucideIconField::new('icon', 'Icone');
    }
}
