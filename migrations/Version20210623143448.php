<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210623143448 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produit (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, stock INT NOT NULL, description LONGTEXT NOT NULL, INDEX IDX_29A5EC2712469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produit_tag (produit_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_423DC0FAF347EFB (produit_id), INDEX IDX_423DC0FABAD26311 (tag_id), PRIMARY KEY(produit_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag_produit (tag_id INT NOT NULL, produit_id INT NOT NULL, INDEX IDX_1B94C18DBAD26311 (tag_id), INDEX IDX_1B94C18DF347EFB (produit_id), PRIMARY KEY(tag_id, produit_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC2712469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE produit_tag ADD CONSTRAINT FK_423DC0FAF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE produit_tag ADD CONSTRAINT FK_423DC0FABAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_produit ADD CONSTRAINT FK_1B94C18DBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_produit ADD CONSTRAINT FK_1B94C18DF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC2712469DE2');
        $this->addSql('ALTER TABLE produit_tag DROP FOREIGN KEY FK_423DC0FAF347EFB');
        $this->addSql('ALTER TABLE tag_produit DROP FOREIGN KEY FK_1B94C18DF347EFB');
        $this->addSql('ALTER TABLE produit_tag DROP FOREIGN KEY FK_423DC0FABAD26311');
        $this->addSql('ALTER TABLE tag_produit DROP FOREIGN KEY FK_1B94C18DBAD26311');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE produit_tag');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE tag_produit');
    }
}
