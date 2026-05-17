<?php

namespace App\Repository\Quote;

use App\Entity\Quote\QuoteResponseValue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QuoteResponseValue>
 */
class QuoteResponseValueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuoteResponseValue::class);
    }
}
