<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260519155553 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fields CHANGE type type ENUM(\'text\', \'email\', \'textarea\', \'select\', \'checkbox\', \'radio\', \'phone\', \'file\', \'date\') NOT NULL');
        $this->addSql('ALTER TABLE quote_response_values ADD updated_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fields CHANGE type type ENUM(\'text\', \'email\', \'textarea\', \'select\', \'checkbox\', \'radio\') NOT NULL');
        $this->addSql('ALTER TABLE quote_response_values DROP updated_at');
    }
}
