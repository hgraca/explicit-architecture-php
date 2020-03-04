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

namespace Acme\App\Test\TestCase\Infrastructure\Persistence\Session;

use Acme\App\Infrastructure\Persistence\Session\KeyValueStorage as SessionKeyValueStorage;
use Acme\App\Test\Framework\AbstractIntegrationTest;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
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
    private const KEY = 'key';
    private const UNKNOWN_KEY = 'unknown_key';
    private const NEW_KEY = 'new_key';
    private const NEW_VALUE = 'new_value';

    /**
     * @var SessionKeyValueStorage
     */
    private $adapter;

    /**
     * @test
     */
    public function get(): void
    {
        $this->adapter->set(self::NAMESPACE, self::NEW_KEY, self::NEW_VALUE);
        self::assertEquals(self::NEW_VALUE, $this->adapter->get(self::NAMESPACE, self::NEW_KEY));
        self::assertNull($this->adapter->get(self::NAMESPACE, self::UNKNOWN_KEY));
    }

    /**
     * @test
     */
    public function set_insert(): void
    {
        self::assertNull($this->adapter->get(self::NAMESPACE, self::NEW_KEY));
        $this->adapter->set(self::NAMESPACE, self::NEW_KEY, self::NEW_VALUE);
        self::assertEquals(self::NEW_VALUE, $this->adapter->get(self::NAMESPACE, self::NEW_KEY));
    }

    /**
     * @test
     */
    public function set_update(): void
    {
        $this->adapter->set(self::NAMESPACE, self::KEY, self::NEW_VALUE);
        self::assertEquals(self::NEW_VALUE, $this->adapter->get(self::NAMESPACE, self::KEY));
    }

    /**
     * @test
     */
    public function remove(): void
    {
        $this->adapter->set(self::NAMESPACE, self::NEW_KEY, self::NEW_VALUE);
        self::assertEquals(self::NEW_VALUE, $this->adapter->get(self::NAMESPACE, self::NEW_KEY));
        $this->adapter->remove(self::NAMESPACE, self::NEW_KEY);
        self::assertNull($this->adapter->get(self::NAMESPACE, self::NEW_KEY));
    }

    protected function setUp(): void
    {
        /** @var SessionInterface $session */
        $session = self::getService('session');
        $this->adapter = new SessionKeyValueStorage($session);
    }
}
