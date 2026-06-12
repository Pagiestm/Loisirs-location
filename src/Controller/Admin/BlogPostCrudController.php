<?php

namespace App\Controller\Admin;

use App\Entity\BlogPost;
use App\Entity\User;
use App\Repository\BlogPostRepository;
use App\Repository\NewsletterRepository;
use App\Service\MailService;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;

class BlogPostCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BlogPost::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Article de blog')
            ->setEntityLabelInPlural('Articles de blog')
            ->setSearchFields(['title', 'excerpt', 'content'])
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPaginatorPageSize(20);
    }

    public function configureActions(Actions $actions): Actions
    {
        $publish = Action::new('publish', 'Publier', 'fa fa-check-circle')
            ->linkToUrl(function (BlogPost $post) {
                return $this->container->get(AdminUrlGenerator::class)
                    ->setController(self::class)
                    ->setAction('publishPost')
                    ->setEntityId($post->getId())
                    ->generateUrl();
            })
            ->displayIf(fn(BlogPost $post) => $post->getStatus() === 'draft');

        $unpublish = Action::new('unpublish', 'Dépublier', 'fa fa-times-circle')
            ->linkToUrl(function (BlogPost $post) {
                return $this->container->get(AdminUrlGenerator::class)
                    ->setController(self::class)
                    ->setAction('unpublishPost')
                    ->setEntityId($post->getId())
                    ->generateUrl();
            })
            ->displayIf(fn(BlogPost $post) => $post->getStatus() === 'published');

        $sendToNewsletter = Action::new('sendToNewsletter', 'Envoyer à la newsletter', 'fa fa-paper-plane')
            ->linkToUrl(function (BlogPost $post) {
                return $this->container->get(AdminUrlGenerator::class)
                    ->setController(self::class)
                    ->setAction('sendMailToNewsletter')
                    ->setEntityId($post->getId())
                    ->generateUrl();
            })
            ->displayIf(fn(BlogPost $post) => $post->getStatus() === 'published');

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $publish)
            ->add(Crud::PAGE_INDEX, $unpublish)
            ->add(Crud::PAGE_EDIT, $publish)
            ->add(Crud::PAGE_EDIT, $unpublish)
            ->add(Crud::PAGE_INDEX, $sendToNewsletter)
            ->add(Crud::PAGE_DETAIL, $sendToNewsletter);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('title', 'Titre')
            ->setColumns('col-12')
            ->setHelp('Le titre de l\'article de blog');

        yield SlugField::new('slug', 'Slug')
            ->setTargetFieldName('title')
            ->setColumns('col-12')
            ->setHelp('URL de l\'article (généré automatiquement depuis le titre)')
            ->hideOnIndex();

        yield ChoiceField::new('status', 'Statut')
            ->setChoices([
                'Brouillon' => 'draft',
                'Publié' => 'published',
            ])
            ->renderAsBadges([
                'draft' => 'warning',
                'published' => 'success',
            ]);

        yield FormField::addFieldset('Contenu')->onlyOnForms();

        yield TextareaField::new('excerpt', 'Résumé')
            ->setColumns('col-12')
            ->setNumOfRows(3)
            ->setHelp('Court résumé de l\'article (affiché dans la liste des articles)')
            ->hideOnIndex();

        yield TextEditorField::new('content', 'Contenu')
            ->setColumns('col-12')
            ->setNumOfRows(20)
            ->hideOnIndex()
            ->setHelp('Contenu complet de l\'article avec mise en forme');

        yield FormField::addFieldset('Image à la une')->onlyOnForms();

        $imageField = ImageField::new('featuredImage', 'Image à la une')
            ->setBasePath('uploads/blog')
            ->setUploadDir('public/uploads/blog')
            ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]')
            ->setColumns('col-12')
            ->setHelp('Image principale de l\'article (recommandé : 1200x630px)')
            ->hideOnIndex();

        if ($pageName === Crud::PAGE_NEW) {
            $imageField->setRequired(true)->setFormTypeOptions(['required' => true]);
        } else {
            $imageField->setRequired(false)->setFormTypeOptions(['required' => false]);
        }

        yield $imageField;

        yield DateTimeField::new('createdAt', 'Créé le')
            ->hideOnForm();

        yield DateTimeField::new('publishedAt', 'Date de publication')
            ->hideOnForm()
            ->hideOnIndex();

        yield DateTimeField::new('updatedAt', 'Modifié le')
            ->hideOnForm()
            ->hideOnIndex();
    }

    public function publishPost(AdminContext $context)
    {
        $entityId = $context->getRequest()->query->get('entityId');
        $em = $this->container->get('doctrine')->getManagerForClass(BlogPost::class);
        $post = $em->getRepository(BlogPost::class)->find($entityId);

        if ($post instanceof BlogPost) {
            $post->setStatus('published');

            if ($post->getPublishedAt() === null) {
                $post->setPublishedAt(new \DateTimeImmutable());
            }

            $em->persist($post);
            $em->flush();

            $this->addFlash('success', 'L\'article a été publié avec succès.');
        }

        $url = $this->container->get(AdminUrlGenerator::class)
            ->setController(self::class)
            ->setAction(Action::INDEX)
            ->generateUrl();

        return $this->redirect($url);
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof BlogPost) {
            if ($entityInstance->getPublishedAt() === null) {
                $entityInstance->setPublishedAt(new \DateTimeImmutable());
            }

            $this->addFlash('success', 'Article créé avec succès.');
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof BlogPost) {
            if ($entityInstance->getStatus() === 'published' && $entityInstance->getPublishedAt() === null) {
                $entityInstance->setPublishedAt(new \DateTimeImmutable());
            }

            $this->addFlash('success', 'Article mis à jour avec succès.');
        }

        parent::updateEntity($entityManager, $entityInstance);
    }

    public function unpublishPost(AdminContext $context)
    {
        $entityId = $context->getRequest()->query->get('entityId');
        $em = $this->container->get('doctrine')->getManagerForClass(BlogPost::class);
        $post = $em->getRepository(BlogPost::class)->find($entityId);

        if ($post instanceof BlogPost) {
            $post->setStatus('draft');

            $em->persist($post);
            $em->flush();

            $this->addFlash('success', 'L\'article a été dépublié avec succès.');
        }

        $url = $this->container->get(AdminUrlGenerator::class)
            ->setController(self::class)
            ->setAction(Action::INDEX)
            ->generateUrl();

        return $this->redirect($url);
    }

    public function createEntity(string $entityFqcn): BlogPost
    {
        $post = new BlogPost();

        $user = $this->getUser();
        if ($user instanceof User) {
            $post->setAuthor($user);
        }

        $post->setPublishedAt(new \DateTimeImmutable());

        return $post;
    }

    public function sendMailToNewsletter(AdminContext $context, MailService $mailService, NewsletterRepository $newsletterRepository, BlogPostRepository $blogPostRepository): RedirectResponse
    {
        $entityId = $context->getRequest()->query->get('entityId');
        $allNewsletterEmails = $newsletterRepository->getAllEmails();

        $post = $blogPostRepository->find($entityId);

        if (!$post) {
            throw new \InvalidArgumentException('Article non trouvé');
        }

        foreach ($allNewsletterEmails as $email) {
            $mailService->sendMjmlEmail(
                subject: 'Nouvel article de blog publié : ' . $post->getTitle(),
                mjmlTemplate: 'email/new_blog_post.mjml.twig',
                context: [
                    'post' => $post,
                    'recipientEmail' => $email,
                ],
                to: $email,
            );
        }

        $url = $this->container->get(AdminUrlGenerator::class)
            ->setController(self::class)
            ->setAction(Action::INDEX)
            ->generateUrl();

        return $this->redirect($url);
    }
}
