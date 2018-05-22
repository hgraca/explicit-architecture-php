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

namespace Acme\App\Infrastructure\Persistence\Doctrine;

use Acme\App\Core\Port\Persistence\KeyValueStorageInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;

/**
 * @author Coen Moij
 * @author Marcos Loureiro
 * @author Kasper Agg
 * @author Herberto Graca <herberto.graca@gmail.com>
 */
final class KeyValueStorage implements KeyValueStorageInterface
{
    private const TABLE = 'KeyValueStorage';
    private const KEY_KEY = 'key';
    private const VALUE_KEY = 'value';

    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @throws DBALException
     */
    public function get(string $namespace, string $key): ?string
    {
        $query = sprintf(
            'SELECT `%s` FROM `%s` WHERE `%s` = :%s',
            self::VALUE_KEY,
            self::TABLE,
            self::KEY_KEY,
            self::KEY_KEY
        );

        $result = $this->connection->fetchColumn(
            $query,
            [self::KEY_KEY => $this->createKey($namespace, $key)]
        );

        return $result === false ? null : (string) $result;
    }

    /**
     * @throws DBALException
     */
    public function set(string $namespace, string $key, string $value): void
    {
        $generatedKey = $this->createKey($namespace, $key);

        if ($this->has($namespace, $key)) {
            $this->update($generatedKey, $value);

            return;
        }
        $this->insert($generatedKey, $value);
    }

    /**
     * @throws DBALException
     */
    public function has(string $namespace, string $key): bool
    {
        return $this->get($namespace, $key) !== null;
    }

    /**
     * @throws DBALException
     */
    public function remove(string $namespace, string $key): void
    {
        $generatedKey = $this->createKey($namespace, $key);

        if ($this->has($namespace, $key)) {
            $this->delete($generatedKey);
        }
    }

    /**
     * @throws DBALException
     */
    private function insert($key, $value): void
    {
        $this->connection->insert(
            self::TABLE,
            $this->escapeKeys([
                self::KEY_KEY => $key,
                self::VALUE_KEY => $value,
            ])
        );
    }

    /**
     * @throws DBALException
     */
    private function update($key, $value): void
    {
        $this->connection->update(
            self::TABLE,
            $this->escapeKeys([self::VALUE_KEY => $value]),
            $this->escapeKeys([self::KEY_KEY => $key])
        );
    }

    /**
     * @throws DBALException
     */
    private function delete($key): void
    {
        $this->connection->delete(
            self::TABLE,
            $this->escapeKeys([self::KEY_KEY => $key])
        );
    }

    private function escapeKeys(array $data): array
    {
        $escapedData = [];
        foreach ($data as $key => $value) {
            $escapedData["`{$key}`"] = $value;
        }

        return $escapedData;
    }

    private function createKey(string $namespace, string $key): string
    {
        return sprintf('%s_%s', $namespace, $key);
    }
}
