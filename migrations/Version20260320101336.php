<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260320101336 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE van_options ADD id_van_option INT AUTO_INCREMENT NOT NULL, CHANGE value value VARCHAR(50) NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id_van_option)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE van_options MODIFY id_van_option INT NOT NULL');
        $this->addSql('ALTER TABLE van_options DROP id_van_option, CHANGE value value VARCHAR(50) DEFAULT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id_van, id_option)');
    }
}
