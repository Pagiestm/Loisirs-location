<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260519073653 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quote_response_values ADD id INT AUTO_INCREMENT NOT NULL, CHANGE value value LONGTEXT DEFAULT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE quote_responses ADD id_van INT DEFAULT NULL');
        $this->addSql('ALTER TABLE quote_responses ADD CONSTRAINT FK_B6BD06EEE7753313 FOREIGN KEY (id_van) REFERENCES vans (id_van)');
        $this->addSql('CREATE INDEX IDX_B6BD06EEE7753313 ON quote_responses (id_van)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quote_response_values MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE quote_response_values DROP id, CHANGE value value LONGTEXT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id_field, id_quote_response)');
        $this->addSql('ALTER TABLE quote_responses DROP FOREIGN KEY FK_B6BD06EEE7753313');
        $this->addSql('DROP INDEX IDX_B6BD06EEE7753313 ON quote_responses');
        $this->addSql('ALTER TABLE quote_responses DROP id_van');
    }
}
