<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240226233443 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("INSERT INTO `shared__user` (`id`, `username`, `roles`, `password`, `is_enabled`, `is_deleted`, `created_at`, `updated_at`, `deleted_at`, `first_name`, `last_name`, `phone_number`, `picture`) VALUES (NULL, 'admin', '[\"ROLE_ADMIN\"]', '\$argon2id\$v=19\$m=65536,t=4,p=1\$VKjBvMQkwkAerxxl9Uimww\$KSTgy/tUsiZS+DMwtX8gS1BSy5P0iRX18nihidU/k+M', '1', '0', '2024-02-26 22:23:02', '2024-02-26 22:23:02', NULL, 'first_name', 'last_name', NULL, NULL)");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shared__user DROP first_name, DROP last_name, DROP phone_number, DROP picture');
    }
}
