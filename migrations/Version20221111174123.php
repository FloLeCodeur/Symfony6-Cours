<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221111174123 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE purchase_details (id INT AUTO_INCREMENT NOT NULL, products_id INT DEFAULT NULL, purchase_id INT NOT NULL, product_name VARCHAR(255) NOT NULL, product_price INT NOT NULL, quantity INT NOT NULL, total INT NOT NULL, INDEX IDX_69FCC1F36C8A81A9 (products_id), INDEX IDX_69FCC1F3558FBEB9 (purchase_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE purchase_details ADD CONSTRAINT FK_69FCC1F36C8A81A9 FOREIGN KEY (products_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE purchase_details ADD CONSTRAINT FK_69FCC1F3558FBEB9 FOREIGN KEY (purchase_id) REFERENCES purchase (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchase_details DROP FOREIGN KEY FK_69FCC1F36C8A81A9');
        $this->addSql('ALTER TABLE purchase_details DROP FOREIGN KEY FK_69FCC1F3558FBEB9');
        $this->addSql('DROP TABLE purchase_details');
    }
}
