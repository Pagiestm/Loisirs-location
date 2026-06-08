<?php

namespace App\Controller\Admin\Quote;

use App\Entity\Quote\QuoteResponse;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mime\Address;

class QuoteResponseCrudController extends AbstractCrudController
{
    public function __construct(
        private EntityManagerInterface $em,
        private MailService $mailService,
        private AdminUrlGenerator $adminUrlGenerator,
    ) {}

    public static function getEntityFqcn(): string
    {
        return QuoteResponse::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $delete = Action::new('deleteWithMail', 'Supprimer et prévenir par email')
            ->linkToCrudAction('deleteWithMail')
            ->addCssClass('dropdown-item action-delete dropdown-item-variant-danger')
            ->setIcon('fa-regular fa-trash-can')
            ->askConfirmation(
                'Êtes-vous sûr de vouloir supprimer cet élément et envoyer un e-mail de notification ?',
                'Supprimer et notifier'
            );

        return $actions
            ->disable(Action::NEW, Action::EDIT)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $delete)
            ->add(Crud::PAGE_DETAIL, $delete)
        ;
    }

    public function deleteWithMail(RequestStack $requestStack): RedirectResponse
    {
        $request = $requestStack->getCurrentRequest();
        $id = $request->query->get('entityId');

        if (!$id) {
            throw $this->createNotFoundException('ID manquant');
        }

        $quoteResponse = $this->em->getRepository(QuoteResponse::class)->find($id);

        if (!$quoteResponse) {
            throw $this->createNotFoundException('Entité introuvable');
        }

        $user = $quoteResponse->getUser();

        if ($user) {
            $this->mailService->sendMjmlEmail(
                subject: 'Suppression de votre demande de devis',
                mjmlTemplate: 'email/quote_deleted.mjml.twig',
                context: [
                    'user' => $user,
                    'van' => $quoteResponse->getVan(),
                    'quoteResponse' => $quoteResponse,
                ],
                to: $user->getEmail()
            );
        }

        $this->em->remove($quoteResponse);
        $this->em->flush();

        $this->addFlash('success', 'Suppression effectuée et email de notification envoyé à l\'utilisateur.');

        return $this->redirect(
            $this->adminUrlGenerator->setController(self::class)->setAction('index')->generateUrl()
        );
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
        yield AssociationField::new('user', 'Email de l\'utilisateur')
            ->formatValue(function ($value, $entity) {
                /** @var QuoteResponse $entity */
                return $entity->getUser() ? '<a href="mailto:' . $entity->getUser()->getEmail() . '">' . $entity->getUser()->getEmail() . '</a>' : 'N/A';
            })
            ->hideOnForm();
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
