<?php

namespace App\Controller;

use App\Entity\Quote\QuoteResponse;
use App\Entity\Van\Van;
use App\Repository\Van\VanRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class QuoteController extends AbstractController
{
    #[Route('/devis/{id}', name: 'app_quote')]
    public function index(int $id, VanRepository $vanRepository, Request $request): Response
    {
        /** @var ?Van $van */
        $van = $vanRepository->find($id);

        if (!$van) {
            throw $this->createNotFoundException('Van introuvable.');
        }

        $quote = $van->getQuote();

        if (!$quote) {
            throw $this->createNotFoundException('Devis non trouvé pour ce van.');
        }

        // Générer un formulaire à partir du des champs du devis
        $quoteResponse = new QuoteResponse();
        $quoteResponse
            ->setQuote($quote)
            ->setUser($this->getUser())
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

            $form->add((string) $field->getId(), $field->getType()->formType(), $options);
        }

        $form = $form->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dd("Formulaire soumis avec succès !", $form->getData());
        }

        return $this->render('pages/quotes/index.html.twig', [
            'quote' => $quote,
            'quoteResponse' => $quoteResponse,
            'form' => $form
        ]);
    }
}
