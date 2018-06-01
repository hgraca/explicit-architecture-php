<?php

declare(strict_types=1);

/*
 * This file is part of the Explicit Architecture POC,
 * which is created on top of the Symfony Demo application.
 *
 * (c) Herberto Graça <herberto.graca@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Acme\App\Build\Migration\Version;

use Acme\App\Build\Migration\ConfigurationAwareMigration;
use Acme\App\Build\Migration\MigrationHelperTrait;
use Doctrine\DBAL\Schema\Schema;

final class Version20180118095302 extends ConfigurationAwareMigration
{
    use MigrationHelperTrait;

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function up(Schema $schema): void
    {
        $engine = $this->connection->getDatabasePlatform()->getName();
        switch ($engine) {
            case 'mysql':
                $this->addSql(
                    'CREATE TABLE KeyValueStorage (`key` VARCHAR(255) PRIMARY KEY, `value` VARCHAR(255) DEFAULT NULL) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB'
                );
                break;
            case 'sqlite':
                $this->addSql('CREATE TABLE KeyValueStorage (`key` TEXT PRIMARY KEY, `value` TEXT DEFAULT NULL)');
                break;
            default:
                $this->abortIf(true, "Unknown engine type '$engine'.");
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE KeyValueStorage');
    }
}
