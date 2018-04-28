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

namespace Acme\App\Core\Component\User\Application\Repository;

use Acme\App\Core\Component\User\Domain\Entity\User;
use Acme\App\Core\Component\User\Domain\Entity\UserId;
use Acme\App\Core\Port\Persistence\ResultCollectionInterface;

interface UserRepositoryInterface
{
    public function upsert(User $user): void;

    public function delete(User $user): void;

    /**
     * @return User[]
     */
    public function findAll(array $orderBy, int $maxResults): ResultCollectionInterface;

    public function findOneByUsername(string $username): User;

    public function findOneByEmail(string $email): User;

    public function findOneById(UserId $id): User;
}
