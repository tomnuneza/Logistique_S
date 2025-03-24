<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250324151735 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD point_de_vente_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6493F95E273 FOREIGN KEY (point_de_vente_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6493F95E273 ON user (point_de_vente_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6493F95E273');
        $this->addSql('DROP INDEX IDX_8D93D6493F95E273 ON user');
        $this->addSql('ALTER TABLE user DROP point_de_vente_id');
    }
}
