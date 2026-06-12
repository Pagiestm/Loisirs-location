<?php

namespace App\Twig\Components;

use App\Form\NewsletterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class Newsletter extends AbstractController
{
    use DefaultActionTrait, ComponentWithFormTrait;

    #[LiveProp]
    public ?Newsletter $initialFormData = null;

    protected function instantiateForm(): FormInterface
    {
        // we can extend AbstractController to get the normal shortcuts
        return $this->createForm(NewsletterType::class, $this->initialFormData);
    }

    #[LiveAction]
    public function save(EntityManagerInterface $entityManager, Request $request)
    {
        $this->submitForm();

        /** @var Newsletter $newsletter */
        $newsletter = $this->getForm()->getData();
        $entityManager->persist($newsletter);
        $entityManager->flush();

        $this->addFlash('success', 'Merci pour votre inscription à notre newsletter !');
        // redirect then -> #newsletter_email
        return $this->redirect($request->headers->get('referer') . '#newsletter_email');
    }
}
