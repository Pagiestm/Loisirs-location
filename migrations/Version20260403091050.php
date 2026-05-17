<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260403091050 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quotes DROP FOREIGN KEY `FK_A1B588C5E7753313`');
        $this->addSql('DROP INDEX IDX_A1B588C5E7753313 ON quotes');
        $this->addSql('ALTER TABLE quotes DROP id_van');
        $this->addSql('ALTER TABLE vans ADD id_quote INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vans ADD CONSTRAINT FK_1477E7E485F48AC4 FOREIGN KEY (id_quote) REFERENCES quotes (id_quote)');
        $this->addSql('CREATE INDEX IDX_1477E7E485F48AC4 ON vans (id_quote)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quotes ADD id_van INT NOT NULL');
        $this->addSql('ALTER TABLE quotes ADD CONSTRAINT `FK_A1B588C5E7753313` FOREIGN KEY (id_van) REFERENCES vans (id_van) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_A1B588C5E7753313 ON quotes (id_van)');
        $this->addSql('ALTER TABLE vans DROP FOREIGN KEY FK_1477E7E485F48AC4');
        $this->addSql('DROP INDEX IDX_1477E7E485F48AC4 ON vans');
        $this->addSql('ALTER TABLE vans DROP id_quote');
    }
}
