<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class GoogleReviewsService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly CacheInterface $cache,
        private readonly string $serpapiKey,
        private readonly string $googlePlaceId,
    ) {}

    /**
     * Retourne les données complètes de la fiche (avis + note globale + lien Maps).
     * Mis en cache 2 semaines.
     *
     * @return array{rating: float, count: int, mapsUrl: string, reviews: array}
     */
    public function getPlaceData(int $maxReviews = 5): array
    {
        $empty = ['rating' => 0, 'count' => 0, 'mapsUrl' => '', 'reviews' => []];

        if (!$this->serpapiKey || !$this->googlePlaceId) {
            return $empty;
        }

        return $this->cache->get('google_place_data_' . $this->googlePlaceId, function (ItemInterface $item) use ($maxReviews, $empty) {
            $item->expiresAfter(1209600); // 2 semaines

            $response = $this->httpClient->request('GET', 'https://serpapi.com/search.json', [
                'query' => [
                    'engine'   => 'google_maps_reviews',
                    'place_id' => $this->googlePlaceId,
                    'api_key'  => $this->serpapiKey,
                    'hl'       => 'fr',
                ],
            ]);

            $data = $response->toArray(false);

            // Note globale
            $placeInfo = $data['place_info'] ?? [];
            $rating    = (float) ($placeInfo['rating']  ?? 0);
            $count     = (int)   ($placeInfo['reviews'] ?? 0);
            $mapsUrl   = $data['search_metadata']['google_maps_reviews_url'] ?? '';

            // Avis (5 derniers, triés par date = ordre par défaut)
            $reviews = [];
            foreach ($data['reviews'] ?? [] as $review) {
                $note  = (int) ($review['rating'] ?? 0);
                $texte = trim($review['snippet'] ?? '');
                if (!$texte) continue;

                $nom      = $review['user']['name'] ?? 'Anonyme';
                $initiale = mb_strtoupper(mb_substr($nom, 0, 1));
                $date     = $review['date'] ?? '';

                $reviews[] = compact('nom', 'initiale', 'texte', 'note', 'date');

                if (count($reviews) >= $maxReviews) break;
            }

            return compact('rating', 'count', 'mapsUrl', 'reviews');
        });
    }
}
