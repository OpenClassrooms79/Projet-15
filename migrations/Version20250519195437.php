<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250519195437 extends AbstractMigration
{
    public function getDescription(): string
    {
        return "Supprime le préfixe 'uploads/' dans le champ 'path' de la table 'media'";
    }

    public function up(Schema $schema): void
    {
        // Supprime le préfixe 'uploads/' dans le champ 'path' de la table 'media'
        $this->addSql("UPDATE media SET path = REPLACE(path, 'uploads/', '') WHERE path LIKE 'uploads/%'");
    }

    public function down(Schema $schema): void
    {
        // Remet le préfixe 'uploads/' si jamais on revient en arrière
        $this->addSql("UPDATE media SET path = CONCAT('uploads/', path) WHERE path NOT LIKE 'uploads/%'");
    }
}
