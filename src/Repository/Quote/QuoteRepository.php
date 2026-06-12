<?php

namespace App\Repository\Quote;

use App\Entity\Quote\Quote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Quote>
 */
class QuoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quote::class);
    }

    public function countAll(): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select('COUNT(q.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countByQuoteType(): array
    {
        return $this->createQueryBuilder('q')
            ->select('q.name AS name')
            ->addSelect('COUNT(qr.id) AS total')
            ->leftJoin('q.quoteResponses', 'qr')
            ->groupBy('q.id')
            ->orderBy('total', 'DESC')
            ->getQuery()
            ->getResult();
    }
}