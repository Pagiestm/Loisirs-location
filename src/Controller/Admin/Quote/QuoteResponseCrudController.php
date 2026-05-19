<?php

namespace App\Controller\Admin\Quote;

use App\Entity\Quote\QuoteResponse;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;

class QuoteResponseCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return QuoteResponse::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::NEW, Action::EDIT);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Réponse de devis')
            ->setEntityLabelInPlural('Réponses de devis')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield AssociationField::new('user', 'Utilisateur')->hideOnForm();
        yield AssociationField::new('van', 'Van')->hideOnForm();
        yield AssociationField::new('quote', 'Formulaire de devis associé')->hideOnForm();
        yield DateTimeField::new('createdAt', 'Date de demande')
            ->setFormat('dd/MM/yyyy HH:mm')
            ->hideOnForm();

        // En mode détail, on affiche un template spécifique pour voir les réponses de l'utilisateur
        if ($pageName === Crud::PAGE_DETAIL) {
            yield AssociationField::new('quoteResponseValues', 'Réponses')
                ->setTemplatePath('admin/fields/quote_response_values.html.twig');
        }
    }
}
