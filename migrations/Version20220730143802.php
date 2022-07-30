<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220730143802 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trick DROP INDEX IDX_D8F0A91E3569D950, ADD UNIQUE INDEX UNIQ_D8F0A91E3569D950 (featured_image_id)');
        $this->addSql('ALTER TABLE trick DROP FOREIGN KEY FK_D8F0A91E3569D950');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91E3569D950 FOREIGN KEY (featured_image_id) REFERENCES media (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trick DROP INDEX UNIQ_D8F0A91E3569D950, ADD INDEX IDX_D8F0A91E3569D950 (featured_image_id)');
        $this->addSql('ALTER TABLE trick DROP FOREIGN KEY FK_D8F0A91E3569D950');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91E3569D950 FOREIGN KEY (featured_image_id) REFERENCES media (id)');
    }
}
