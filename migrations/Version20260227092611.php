<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260227092611 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE van_images (id INT AUTO_INCREMENT NOT NULL, image_name VARCHAR(255) DEFAULT NULL, position SMALLINT DEFAULT 0 NOT NULL, caption VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, van_id INT NOT NULL, INDEX IDX_12E43C508A128D90 (van_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE van_images ADD CONSTRAINT FK_12E43C508A128D90 FOREIGN KEY (van_id) REFERENCES vans (id_van) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE van_images DROP FOREIGN KEY FK_12E43C508A128D90');
        $this->addSql('DROP TABLE van_images');
    }
}
