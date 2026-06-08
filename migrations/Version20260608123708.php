<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260608123708 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fields DROP FOREIGN KEY `FK_7EE5E38885F48AC4`');
        $this->addSql('ALTER TABLE fields ADD CONSTRAINT FK_7EE5E38885F48AC4 FOREIGN KEY (id_quote) REFERENCES quotes (id_quote) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fields DROP FOREIGN KEY FK_7EE5E38885F48AC4');
        $this->addSql('ALTER TABLE fields ADD CONSTRAINT `FK_7EE5E38885F48AC4` FOREIGN KEY (id_quote) REFERENCES quotes (id_quote) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
