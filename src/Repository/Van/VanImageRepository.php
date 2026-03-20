<?php

namespace App\Repository\Van;

use App\Entity\Van\VanImage;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Gedmo\Sortable\Entity\Repository\SortableRepository;

/**
 * @extends SortableRepository<VanImage>
 */
class VanImageRepository extends SortableRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        $entityManager = $registry->getManagerForClass(VanImage::class);

        if (!$entityManager instanceof EntityManagerInterface) {
            throw new \RuntimeException(sprintf('No ORM entity manager found for "%s".', VanImage::class));
        }

        parent::__construct($entityManager, $entityManager->getClassMetadata(VanImage::class));
    }
}
