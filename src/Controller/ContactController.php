<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Service\MailService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact', methods: ['GET', 'POST'])]
    public function index(Request $request, MailService $mailService, LoggerInterface $logger): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->processContactForm($form->getData(), $mailService);

                $this->addFlash('success', 'Votre message a bien été envoyé. Notre équipe vous répondra rapidement.');

                return $this->redirectToRoute('app_contact');
            } catch (\Throwable $e) {
                $logger->error('Echec de l\'envoi du formulaire de contact.', ['exception' => $e]);
                $this->addFlash('error', 'Une erreur est survenue lors de l\'envoi du message. Veuillez réessayer.');
            }
        }

        $response = new Response();
        if ($form->isSubmitted() && !$form->isValid()) {
            $response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->render('pages/contact/index.html.twig', [
            'form' => $form,
        ], $response);
    }

    /**
     * @param array<string, string|null> $data
     */
    private function processContactForm(array $data, MailService $mailService): void
    {
        $mailService->sendMjmlEmail(
            subject: 'Nouveau message de contact - Loisirs Location',
            mjmlTemplate: 'email/contact_message.mjml.twig',
            context: [
                'firstName' => $data['firstName'] ?? '',
                'lastName'  => $data['lastName'] ?? '',
                'email'     => $data['email'] ?? '',
                'phone'     => $data['phone'] ?? '',
                'subject'   => $data['subject'] ?? '',
                'message'   => $data['message'] ?? '',
                'sentAt'    => new \DateTimeImmutable(),
            ],
            replyTo: $data['email'] ?? null,
        );
    }
}
