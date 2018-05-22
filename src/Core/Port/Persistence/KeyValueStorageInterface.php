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

namespace Acme\App\Core\Port\Persistence;

/**
 * @author Coen Moij
 * @author Kasper Agg
 * @author Herberto Graca <herberto.graca@gmail.com>
 */
interface KeyValueStorageInterface
{
    public function get(string $namespace, string $key): ?string;

    public function set(string $namespace, string $key, string $value): void;

    public function has(string $namespace, string $key): bool;

    public function remove(string $namespace, string $key): void;
}
