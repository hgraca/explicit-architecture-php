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

use Acme\App\Core\SharedKernel\Component\User\Domain\User\UserId;

/**
 * @author Coen Moij
 * @author Herberto Graca <herberto.graca@gmail.com>
 */
interface UserKeyValueStorageInterface
{
    public function get(UserId $userId, string $namespace, string $key): ?string;

    public function set(UserId $userId, string $namespace, string $key, string $value): void;

    public function has(UserId $userId, string $namespace, string $key): bool;
}
