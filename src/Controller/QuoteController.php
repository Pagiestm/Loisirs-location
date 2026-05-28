<?php

namespace App\Controller;

use App\Entity\Quote\QuoteResponse;
use App\Entity\Quote\QuoteResponseValue;
use App\Entity\User;
use App\Entity\Van\Van;
use App\Repository\Van\VanRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\MailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class QuoteController extends AbstractController
{
    #[Route('/devis/{id}', name: 'app_quote')]
    public function index(int $id, VanRepository $vanRepository, Request $request, EntityManagerInterface $em, MailService $mailService): Response
    {
        $user = $this->getUser();

        if (!$user || !$user instanceof User) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page.');
            return $this->redirectToRoute('app_home', ['open_login' => 'true']);
        }

        /** @var ?Van $van */
        $van = $vanRepository->find($id);

        if (!$van) {
            throw $this->createNotFoundException('Van introuvable.');
        }

        $quote = $van->getQuote();

        if (!$quote) {
            throw $this->createNotFoundException('Devis non trouvé pour ce van.');
        }

        if ($this->getUser()) {
            $existingResponse = $em->getRepository(QuoteResponse::class)->findOneBy(
                ['quote' => $quote, 'user' => $this->getUser(), 'van' => $van],
                ['createdAt' => 'DESC']
            );

            if ($existingResponse && $existingResponse->getCreatedAt() > new \DateTimeImmutable('-24 hours')) {
                $this->addFlash('warning', 'Vous devez attendre 24h avant de pouvoir refaire une demande de devis pour ce van.');
                return $this->redirectToRoute('app_vans_show', ['id' => $id]);
            }
        }

        // Générer un formulaire à partir du des champs du devis
        $quoteResponse = new QuoteResponse();
        $quoteResponse
            ->setQuote($quote)
            ->setUser($this->getUser())
            ->setVan($van)
        ;

        $form = $this->createFormBuilder($quoteResponse);

        foreach ($quote->getFields() as $field) {
            $options = $field->getOptions();

            // On reformate la liste de choix en tableau associatif attendu par Symfony
            // tout en ayant conservé l'ordre d'insertion.
            if (isset($options['choices_list'])) {
                $choices = [];
                foreach ($options['choices_list'] as $choice) {
                    $choices[$choice['label']] = $choice['value'];
                }
                $options['choices'] = $choices;
                unset($options['choices_list']);
            }

            $constraints = $options['constraints'] ?? [];

            if ($field->getType() === \App\Enum\FieldEnum::PHONE) {
                $constraints[] = new \Symfony\Component\Validator\Constraints\Regex([
                    'pattern' => '/^[0-9\-\+\s\(\)]{10,20}$/',
                    'message' => 'Le numéro de téléphone n\'est pas valide.'
                ]);
            } elseif ($field->getType() === \App\Enum\FieldEnum::FILE) {
                $constraints[] = new \Symfony\Component\Validator\Constraints\File([
                    'maxSize' => '5M',
                    'mimeTypes' => [
                        'application/pdf',
                        'application/x-pdf',
                        'image/jpeg',
                        'image/png',
                    ],
                    'mimeTypesMessage' => 'Veuillez transférer un document valide (PDF, JPG, PNG).',
                ]);
            } elseif ($field->getType() === \App\Enum\FieldEnum::DATE) {
                $options['widget'] = 'single_text';
            }

            if (!empty($constraints)) {
                $options['constraints'] = $constraints;
            }

            $form->add((string) $field->getId(), $field->getType()->formType(), $options);
        }

        $form = $form->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var QuoteResponse $quoteResponse */
            $quoteResponse = $form->getData();
            $quoteResponse
                ->setQuote($quote)
                ->setUser($this->getUser())
            ;

            foreach ($quote->getFields() as $field) {
                $fieldId = (string) $field->getId();
                $value = $form->get($fieldId)->getData();
                $quoteResponseValue = new QuoteResponseValue();

                if ($value instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                    $quoteResponseValue->setDocumentFile($value);
                    // VichUploaderBundle will handle the file upload and setting the filename in "value".
                } else {
                    if ($value instanceof \DateTimeInterface) {
                        $value = $value->format('d/m/Y');
                    } elseif (is_array($value)) {
                        $value = empty($value) ? null : implode(', ', $value);
                    }
                    $quoteResponseValue->setValue($value);
                }

                $quoteResponseValue
                    ->setQuoteResponse($quoteResponse)
                    ->setField($field)
                ;
                $em->persist($quoteResponseValue);
            }

            try {
                $em->persist($quoteResponse);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de l\'enregistrement de votre réponse. Veuillez réessayer.');
                return $this->redirectToRoute('app_quote', ['id' => $id]);
            }

            try {
                $em->flush();
                $user = $this->getUser();

                if ($user instanceof User) {
                    $mailService->sendMjmlEmail(
                        subject: 'Confirmation de votre demande de devis',
                        mjmlTemplate: 'email/quote_confirmation.mjml.twig',
                        context: [
                            'van' => $van,
                            'user' => $user,
                        ],
                        to: $user->getEmail()
                    );
                }

                $this->addFlash('success', 'Votre demande de devis a été envoyée avec succès ! Nous vous contacterons dans les plus brefs délais.');
                return $this->redirectToRoute('app_vans_show', ['id' => $id]);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de l\'enregistrement de votre réponse. Veuillez réessayer.');
                return $this->redirectToRoute('app_quote', ['id' => $id]);
            }
        }

        return $this->render('pages/quotes/index.html.twig', [
            'quote' => $quote,
            'quoteResponse' => $quoteResponse,
            'form' => $form
        ]);
    }
}
