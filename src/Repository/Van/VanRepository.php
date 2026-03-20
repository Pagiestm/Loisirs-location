<?php

namespace App\Repository\Van;

use App\Entity\Van\Van;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Van>
 */
class VanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Van::class);
    }

    /** Tous les vans avec options chargées en une seule requête */
    public function findAllWithOptions(): array
    {
        return $this->createQueryBuilder('v')
            ->leftJoin('v.options', 'vo')
            ->leftJoin('vo.option', 'o')
            ->addSelect('vo', 'o')
            ->orderBy('v.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** Un van avec toutes ses options */
    public function findOneWithOptions(int $id): ?Van
    {
        return $this->createQueryBuilder('v')
            ->leftJoin('v.options', 'vo')
            ->leftJoin('vo.option', 'o')
            ->addSelect('vo', 'o')
            ->where('v.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /** Tous les vans avec leurs images pour la galerie */
    public function findAllWithImages(): array
    {
        return $this->createQueryBuilder('v')
            ->leftJoin('v.images', 'i')
            ->addSelect('i')
            ->orderBy('v.name', 'ASC')
            ->addOrderBy('i.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
