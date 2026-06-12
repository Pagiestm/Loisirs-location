<?php

namespace App\Controller\Admin;

use App\Entity\Newsletter;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;

class NewsletterCrudController extends AbstractCrudController
{
    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable('new', 'edit');
    }

    public static function getEntityFqcn(): string
    {
        return Newsletter::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            EmailField::new('email'),
        ];
    }
}
