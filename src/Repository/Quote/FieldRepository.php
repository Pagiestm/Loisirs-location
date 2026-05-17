<?php

namespace App\Repository\Quote;

use App\Entity\Quote\Field;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Gedmo\Sortable\Entity\Repository\SortableRepository;

/**
 * @extends SortableRepository<Field>
 */
class FieldRepository extends SortableRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        $entityManager = $registry->getManagerForClass(Field::class);

        if (!$entityManager instanceof EntityManagerInterface) {
            throw new \RuntimeException(sprintf('No ORM entity manager found for "%s".', Field::class));
        }

        parent::__construct($entityManager, $entityManager->getClassMetadata(Field::class));
    }
}
