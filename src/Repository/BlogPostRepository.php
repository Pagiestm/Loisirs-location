<?php

namespace App\Repository;

use App\Entity\BlogPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BlogPost>
 */
class BlogPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogPost::class);
    }

    /**
     * Récupère tous les articles publiés, triés par date de publication décroissante
     */
    public function findPublished(int $limit = null): array
    {
        $qb = $this->createQueryBuilder('bp')
            ->andWhere('bp.status = :status')
            ->andWhere('bp.publishedAt IS NOT NULL')
            ->andWhere('bp.publishedAt <= :now')
            ->setParameter('status', 'published')
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('bp.publishedAt', 'DESC');

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Récupère un article publié par son slug
     */
    public function findOnePublishedBySlug(string $slug): ?BlogPost
    {
        return $this->createQueryBuilder('bp')
            ->andWhere('bp.slug = :slug')
            ->andWhere('bp.status = :status')
            ->andWhere('bp.publishedAt IS NOT NULL')
            ->andWhere('bp.publishedAt <= :now')
            ->setParameter('slug', $slug)
            ->setParameter('status', 'published')
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Récupère les articles récents (pour sidebar, footer, etc.)
     */
    public function findRecentPublished(int $limit = 5): array
    {
        return $this->findPublished($limit);
    }
}
