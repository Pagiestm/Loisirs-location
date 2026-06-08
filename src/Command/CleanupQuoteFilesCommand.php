<?php

namespace App\Command;

use App\Repository\Quote\QuoteResponseValueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;


/*
 Commande à éxecuter une fois sur le serveur de prod pour supprimer les fichiers de devis vieux de plus de 2 ans (et éviter d'avoir des fichiers orphelins qui prennent de la place)
 > crontab -e
 > 0 3 * * * php /var/www/project/bin/console app:cleanup-quote-files --env=prod
*/

#[AsCommand(
    name: 'app:cleanup-quote-files',
    description: 'Supprime les fichiers quotes vieux de plus de 2 ans'
)]
class CleanupQuoteFilesCommand extends Command
{
    public function __construct(
        private QuoteResponseValueRepository $repo,
        private EntityManagerInterface $em,
        private Filesystem $filesystem,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $limitDate = new \DateTimeImmutable('-2 years');

        // 1. récupérer les entrées concernées
        $items = $this->repo->createQueryBuilder('q')
            ->andWhere('q.updatedAt < :date')
            ->andWhere('q.value IS NOT NULL')
            ->setParameter('date', $limitDate)
            ->getQuery()
            ->getResult();

        $basePath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'quotes';

        $deleted = 0;

        foreach ($items as $item) {
            $fileName = $item->getValue();

            if (!$fileName) {
                continue;
            }

            $filePath = $basePath . DIRECTORY_SEPARATOR . $fileName;

            // suppression fichier physique
            if ($this->filesystem->exists($filePath)) {
                $this->filesystem->remove($filePath);
                // optionnel : suppression DB
                $item->setValue(null);
                $item->setDocumentFile(null);

                $this->em->persist($item);
                $deleted++;
            }
        }

        $this->em->flush();

        $output->writeln(sprintf('%d fichiers supprimés', $deleted));

        return Command::SUCCESS;
    }
}
