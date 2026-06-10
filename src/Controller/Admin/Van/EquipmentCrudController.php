<?php

namespace App\Controller\Admin\Van;

use App\EasyAdmin\Field\LucideIconField;
use App\Entity\Van\Equipment;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class EquipmentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Equipment::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name', 'Nom');
        yield LucideIconField::new('icon', 'Icone');
    }
}
