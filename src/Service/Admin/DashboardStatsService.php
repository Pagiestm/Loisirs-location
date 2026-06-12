<?php

namespace App\Service\Admin;

use App\Repository\Quote\QuoteRepository;
use App\Repository\Quote\QuoteResponseRepository;
use App\Repository\Van\VanRepository;

class DashboardStatsService
{
    public function __construct(
        private QuoteRepository $quoteRepository,
        private QuoteResponseRepository $quoteResponseRepository,
        private VanRepository $vanRepository,
    ) {}

    public function getStats(): array
    {
        return [
            // Formulaires de devis
            'quotes_total' => $this->quoteRepository->countAll(),
            'quotes_by_type' => $this->quoteRepository->countByQuoteType(),

            // Réponses aux devis
            'responses_total' => $this->quoteResponseRepository->countAll(),
            'responses_month' => $this->quoteResponseRepository->countThisMonth(),
            'responses_last_30_days' => $this->quoteResponseRepository->countLast30Days(),
            'responses_evolution' => $this->quoteResponseRepository->countGroupedByDayLast30Days(),

            // Vans
            'vans_total' => count($this->vanRepository->findAll()),
            'van_usage' => $this->quoteResponseRepository->mostRequestedVans(),

            // KPI
            'conversion_rate' => $this->calculateConversionRate(),
        ];
    }

    private function calculateConversionRate(): float
    {
        $quotes = $this->quoteRepository->countAll();
        $responses = $this->quoteResponseRepository->countAll();

        if ($quotes === 0) {
            return 0;
        }

        return round(($responses / $quotes) * 100, 2);
    }
}