<?php

namespace App\Repository;

use App\Entity\Newsletter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Newsletter>
 */
class NewsletterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Newsletter::class);
    }

    /**
     * @return array Returns an array of email addresses
     */
    public function getAllEmails(): array
    {
        $query = $this->createQueryBuilder('n')
            ->select('n.email')
            ->orderBy('n.email', 'ASC')
            ->getQuery()
            ->getResult();

        return array_column($query, 'email');
    }
}
