<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260610082401 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE equipments (id_equipment INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, icon VARCHAR(50) DEFAULT NULL, PRIMARY KEY (id_equipment)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE van_equipments (id_van_option INT AUTO_INCREMENT NOT NULL, value VARCHAR(50) NOT NULL, id_van INT NOT NULL, id_equipment INT NOT NULL, INDEX IDX_B6B05929E7753313 (id_van), INDEX IDX_B6B05929530D69A4 (id_equipment), PRIMARY KEY (id_van_option)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE van_equipments ADD CONSTRAINT FK_B6B05929E7753313 FOREIGN KEY (id_van) REFERENCES vans (id_van)');
        $this->addSql('ALTER TABLE van_equipments ADD CONSTRAINT FK_B6B05929530D69A4 FOREIGN KEY (id_equipment) REFERENCES equipments (id_equipment)');
        $this->addSql('ALTER TABLE van_options DROP FOREIGN KEY `FK_16CBD8B77CB1B55D`');
        $this->addSql('ALTER TABLE van_options DROP FOREIGN KEY `FK_16CBD8B7E7753313`');
        $this->addSql('DROP TABLE options');
        $this->addSql('DROP TABLE van_options');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE options (id_option INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, icon VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, PRIMARY KEY (id_option)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE van_options (id_van_option INT AUTO_INCREMENT NOT NULL, value VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, id_van INT NOT NULL, id_option INT NOT NULL, INDEX IDX_16CBD8B77CB1B55D (id_option), INDEX IDX_16CBD8B7E7753313 (id_van), PRIMARY KEY (id_van_option)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE van_options ADD CONSTRAINT `FK_16CBD8B77CB1B55D` FOREIGN KEY (id_option) REFERENCES options (id_option) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE van_options ADD CONSTRAINT `FK_16CBD8B7E7753313` FOREIGN KEY (id_van) REFERENCES vans (id_van) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE van_equipments DROP FOREIGN KEY FK_B6B05929E7753313');
        $this->addSql('ALTER TABLE van_equipments DROP FOREIGN KEY FK_B6B05929530D69A4');
        $this->addSql('DROP TABLE equipments');
        $this->addSql('DROP TABLE van_equipments');
    }
}
