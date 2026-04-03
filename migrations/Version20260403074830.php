<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260403074830 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE blog_posts (id_blog_post INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, excerpt LONGTEXT DEFAULT NULL, featured_image VARCHAR(255) DEFAULT NULL, status VARCHAR(20) NOT NULL, published_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, id_user INT NOT NULL, UNIQUE INDEX UNIQ_78B2F932989D9B62 (slug), INDEX IDX_78B2F9326B3CA4B (id_user), PRIMARY KEY (id_blog_post)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE blog_posts ADD CONSTRAINT FK_78B2F9326B3CA4B FOREIGN KEY (id_user) REFERENCES users (id_user)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blog_posts DROP FOREIGN KEY FK_78B2F9326B3CA4B');
        $this->addSql('DROP TABLE blog_posts');
    }
}
