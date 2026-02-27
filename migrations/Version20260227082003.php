<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260227082003 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE addresses (id_address INT AUTO_INCREMENT NOT NULL, address VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, postal_code VARCHAR(10) NOT NULL, complement VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id_address)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE fields (id_field INT AUTO_INCREMENT NOT NULL, options JSON NOT NULL, position INT NOT NULL, type VARCHAR(100) NOT NULL, id_quote INT NOT NULL, INDEX IDX_7EE5E38885F48AC4 (id_quote), PRIMARY KEY (id_field)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE options (id_option INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, icon VARCHAR(50) DEFAULT NULL, PRIMARY KEY (id_option)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE quote_response_values (value LONGTEXT NOT NULL, id_field INT NOT NULL, id_quote_response INT NOT NULL, INDEX IDX_BF7F84B9B5700468 (id_field), INDEX IDX_BF7F84B9E9271E6C (id_quote_response), PRIMARY KEY (id_field, id_quote_response)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE quote_responses (id_quote_response INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, id_quote INT NOT NULL, id_user INT NOT NULL, INDEX IDX_B6BD06EE85F48AC4 (id_quote), INDEX IDX_B6BD06EE6B3CA4B (id_user), PRIMARY KEY (id_quote_response)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE quotes (id_quote INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, description LONGTEXT DEFAULT NULL, id_van INT NOT NULL, INDEX IDX_A1B588C5E7753313 (id_van), PRIMARY KEY (id_quote)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE rentals (rental_start_date DATETIME NOT NULL, rental_end_date DATETIME NOT NULL, id_user INT NOT NULL, id_van INT NOT NULL, INDEX IDX_35ACDB486B3CA4B (id_user), INDEX IDX_35ACDB48E7753313 (id_van), PRIMARY KEY (id_user, id_van)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE users (id_user INT AUTO_INCREMENT NOT NULL, last_name VARCHAR(50) NOT NULL, first_name VARCHAR(50) NOT NULL, email VARCHAR(100) NOT NULL, phone VARCHAR(20) DEFAULT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, id_address INT DEFAULT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), INDEX IDX_1483A5E9D3D3C6F1 (id_address), PRIMARY KEY (id_user)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE van_options (value VARCHAR(50) DEFAULT NULL, id_van INT NOT NULL, id_option INT NOT NULL, INDEX IDX_16CBD8B7E7753313 (id_van), INDEX IDX_16CBD8B77CB1B55D (id_option), PRIMARY KEY (id_van, id_option)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE vans (id_van INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, subtitle VARCHAR(50) NOT NULL, description LONGTEXT DEFAULT NULL, image VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id_van)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE fields ADD CONSTRAINT FK_7EE5E38885F48AC4 FOREIGN KEY (id_quote) REFERENCES quotes (id_quote)');
        $this->addSql('ALTER TABLE quote_response_values ADD CONSTRAINT FK_BF7F84B9B5700468 FOREIGN KEY (id_field) REFERENCES fields (id_field)');
        $this->addSql('ALTER TABLE quote_response_values ADD CONSTRAINT FK_BF7F84B9E9271E6C FOREIGN KEY (id_quote_response) REFERENCES quote_responses (id_quote_response)');
        $this->addSql('ALTER TABLE quote_responses ADD CONSTRAINT FK_B6BD06EE85F48AC4 FOREIGN KEY (id_quote) REFERENCES quotes (id_quote)');
        $this->addSql('ALTER TABLE quote_responses ADD CONSTRAINT FK_B6BD06EE6B3CA4B FOREIGN KEY (id_user) REFERENCES users (id_user)');
        $this->addSql('ALTER TABLE quotes ADD CONSTRAINT FK_A1B588C5E7753313 FOREIGN KEY (id_van) REFERENCES vans (id_van)');
        $this->addSql('ALTER TABLE rentals ADD CONSTRAINT FK_35ACDB486B3CA4B FOREIGN KEY (id_user) REFERENCES users (id_user)');
        $this->addSql('ALTER TABLE rentals ADD CONSTRAINT FK_35ACDB48E7753313 FOREIGN KEY (id_van) REFERENCES vans (id_van)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9D3D3C6F1 FOREIGN KEY (id_address) REFERENCES addresses (id_address)');
        $this->addSql('ALTER TABLE van_options ADD CONSTRAINT FK_16CBD8B7E7753313 FOREIGN KEY (id_van) REFERENCES vans (id_van)');
        $this->addSql('ALTER TABLE van_options ADD CONSTRAINT FK_16CBD8B77CB1B55D FOREIGN KEY (id_option) REFERENCES options (id_option)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fields DROP FOREIGN KEY FK_7EE5E38885F48AC4');
        $this->addSql('ALTER TABLE quote_response_values DROP FOREIGN KEY FK_BF7F84B9B5700468');
        $this->addSql('ALTER TABLE quote_response_values DROP FOREIGN KEY FK_BF7F84B9E9271E6C');
        $this->addSql('ALTER TABLE quote_responses DROP FOREIGN KEY FK_B6BD06EE85F48AC4');
        $this->addSql('ALTER TABLE quote_responses DROP FOREIGN KEY FK_B6BD06EE6B3CA4B');
        $this->addSql('ALTER TABLE quotes DROP FOREIGN KEY FK_A1B588C5E7753313');
        $this->addSql('ALTER TABLE rentals DROP FOREIGN KEY FK_35ACDB486B3CA4B');
        $this->addSql('ALTER TABLE rentals DROP FOREIGN KEY FK_35ACDB48E7753313');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9D3D3C6F1');
        $this->addSql('ALTER TABLE van_options DROP FOREIGN KEY FK_16CBD8B7E7753313');
        $this->addSql('ALTER TABLE van_options DROP FOREIGN KEY FK_16CBD8B77CB1B55D');
        $this->addSql('DROP TABLE addresses');
        $this->addSql('DROP TABLE fields');
        $this->addSql('DROP TABLE options');
        $this->addSql('DROP TABLE quote_response_values');
        $this->addSql('DROP TABLE quote_responses');
        $this->addSql('DROP TABLE quotes');
        $this->addSql('DROP TABLE rentals');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE van_options');
        $this->addSql('DROP TABLE vans');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
