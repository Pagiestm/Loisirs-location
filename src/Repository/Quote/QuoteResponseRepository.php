<?php

namespace App\Repository\Quote;

use App\Entity\Quote\QuoteResponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QuoteResponse>
 */
class QuoteResponseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuoteResponse::class);
    }

    public function countAll(): int
    {
        return (int) $this->createQueryBuilder('qr')
            ->select('COUNT(qr.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countThisMonth(): int
    {
        return (int) $this->createQueryBuilder('qr')
            ->select('COUNT(qr.id)')
            ->where('qr.createdAt >= :start')
            ->setParameter(
                'start',
                new \DateTimeImmutable('first day of this month')
            )
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countLast30Days(): int
    {
        return (int) $this->createQueryBuilder('qr')
            ->select('COUNT(qr.id)')
            ->where('qr.createdAt >= :start')
            ->setParameter(
                'start',
                new \DateTimeImmutable('-30 days')
            )
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countGroupedByDayLast30Days(): array
    {
        return $this->createQueryBuilder('qr')
            ->select('SUBSTRING(qr.createdAt, 1, 10) AS day')
            ->addSelect('COUNT(qr.id) AS total')
            ->where('qr.createdAt >= :start')
            ->setParameter(
                'start',
                new \DateTimeImmutable('-30 days')
            )
            ->groupBy('day')
            ->orderBy('day', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function mostRequestedVans(): array
    {
        return $this->createQueryBuilder('qr')
            ->select('v.name AS name')
            ->addSelect('COUNT(qr.id) AS total')
            ->join('qr.van', 'v')
            ->groupBy('v.id')
            ->orderBy('total', 'DESC')
            ->getQuery()
            ->getResult();
    }
}