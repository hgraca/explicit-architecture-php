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

namespace Acme\App\Test\TestCase\Infrastructure\Persistence\Doctrine;

use Acme\App\Infrastructure\Persistence\Doctrine\KeyValueStorage as DoctrineKeyValueStorage;
use Acme\App\Test\Framework\AbstractIntegrationTest;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Coen Moij
 * @author Herberto Graca <herberto.graca@gmail.com>
 * @author Kasper Agg
 *
 * @medium
 *
 * @internal
 */
final class KeyValueStorageIntegrationTest extends AbstractIntegrationTest
{
    private const NAMESPACE = 'namespace';
    private const KEY = 'key_1';
    private const VALUE = 'value';
    private const UNKNOWN_KEY = 'unknown_key';
    private const NEW_KEY = 'new_key';
    private const NEW_VALUE = 'new_value';

    /**
     * @var DoctrineKeyValueStorage
     */
    private $adapter;

    protected function setUp(): void
    {
        /** @var EntityManager $entityManager */
        $entityManager = self::getService(EntityManagerInterface::class);
        $this->adapter = new DoctrineKeyValueStorage($entityManager->getConnection());
    }

    /**
     * @test
     *
     * @throws DBALException
     */
    public function get(): void
    {
        self::assertEquals(
            self::VALUE,
            $this->adapter->get(self::NAMESPACE, self::KEY)
        );
        self::assertNull($this->adapter->get(self::NAMESPACE, self::UNKNOWN_KEY));
    }

    /**
     * @test
     *
     * @throws DBALException
     */
    public function set_insert(): void
    {
        self::assertNull($this->adapter->get(self::NAMESPACE, self::NEW_KEY));
        $this->adapter->set(self::NAMESPACE, self::NEW_KEY, self::NEW_VALUE);
        self::assertEquals(self::NEW_VALUE, $this->adapter->get(self::NAMESPACE, self::NEW_KEY));
    }

    /**
     * @test
     *
     * @throws DBALException
     */
    public function set_update(): void
    {
        $this->adapter->set(self::NAMESPACE, self::KEY, self::NEW_VALUE);
        self::assertEquals(self::NEW_VALUE, $this->adapter->get(self::NAMESPACE, self::KEY));
    }

    /**
     * @test
     *
     * @throws DBALException
     */
    public function remove(): void
    {
        $this->adapter->set(self::NAMESPACE, self::NEW_KEY, self::NEW_VALUE);
        self::assertEquals(self::NEW_VALUE, $this->adapter->get(self::NAMESPACE, self::NEW_KEY));
        $this->adapter->remove(self::NAMESPACE, self::NEW_KEY);
        self::assertNull($this->adapter->get(self::NAMESPACE, self::NEW_KEY));
    }
}
