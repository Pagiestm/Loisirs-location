<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260610090650 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `option` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, id_van INT NOT NULL, INDEX IDX_5A8600B0E7753313 (id_van), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE `option` ADD CONSTRAINT FK_5A8600B0E7753313 FOREIGN KEY (id_van) REFERENCES vans (id_van)');
        $this->addSql('ALTER TABLE van_equipments MODIFY id_van_option INT NOT NULL');
        $this->addSql('ALTER TABLE van_equipments CHANGE id_van_option id_van_equipment INT AUTO_INCREMENT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id_van_equipment)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `option` DROP FOREIGN KEY FK_5A8600B0E7753313');
        $this->addSql('DROP TABLE `option`');
        $this->addSql('ALTER TABLE van_equipments MODIFY id_van_equipment INT NOT NULL');
        $this->addSql('ALTER TABLE van_equipments CHANGE id_van_equipment id_van_option INT AUTO_INCREMENT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id_van_option)');
    }
}
