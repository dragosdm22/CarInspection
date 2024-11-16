<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240720134201 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE inspections (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', car_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', inspection_type SMALLINT NOT NULL, inspection_date DATE NOT NULL, INDEX IDX_86254990A76ED395 (user_id), INDEX IDX_86254990C3C6F69F (car_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE inspections ADD CONSTRAINT FK_86254990A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE inspections ADD CONSTRAINT FK_86254990C3C6F69F FOREIGN KEY (car_id) REFERENCES cars (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inspections DROP FOREIGN KEY FK_86254990A76ED395');
        $this->addSql('ALTER TABLE inspections DROP FOREIGN KEY FK_86254990C3C6F69F');
        $this->addSql('DROP TABLE inspections');
    }
}
