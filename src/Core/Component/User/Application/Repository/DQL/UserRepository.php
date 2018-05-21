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

namespace Acme\App\Core\Component\User\Application\Repository\DQL;

use Acme\App\Core\Component\User\Application\Repository\UserRepositoryInterface;
use Acme\App\Core\Component\User\Domain\User\User;
use Acme\App\Core\Port\Persistence\DQL\DqlQueryBuilderInterface;
use Acme\App\Core\Port\Persistence\PersistenceServiceInterface;
use Acme\App\Core\Port\Persistence\QueryServiceRouterInterface;
use Acme\App\Core\Port\Persistence\ResultCollectionInterface;
use Acme\App\Core\SharedKernel\Component\User\Domain\User\UserId;

/**
 * This custom Doctrine repository is empty because so far we don't need any custom
 * method to query for application user information. But it's always a good practice
 * to define a custom repository that will be used when the application grows.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 * @author Herberto Graca <herberto.graca@gmail.com>
 */
class UserRepository implements UserRepositoryInterface
{
    /**
     * @var DqlQueryBuilderInterface
     */
    private $dqlQueryBuilder;

    /**
     * @var QueryServiceRouterInterface
     */
    private $queryService;

    /**
     * @var PersistenceServiceInterface
     */
    private $persistenceService;

    public function __construct(
        DqlQueryBuilderInterface $dqlQueryBuilder,
        QueryServiceRouterInterface $queryService,
        PersistenceServiceInterface $persistenceService
    ) {
        $this->dqlQueryBuilder = $dqlQueryBuilder;
        $this->queryService = $queryService;
        $this->persistenceService = $persistenceService;
    }

    public function add(User $user): void
    {
        $this->persistenceService->upsert($user);
    }

    public function remove(User $user): void
    {
        $this->persistenceService->delete($user);
    }

    /**
     * @return User[]
     */
    public function findAll(array $orderByList = ['id' => 'DESC'], int $maxResults = null): ResultCollectionInterface
    {
        $this->dqlQueryBuilder->create(User::class);

        foreach ($orderByList as $property => $direction) {
            $this->dqlQueryBuilder->orderBy('User.' . $property, $direction);
        }

        if ($maxResults) {
            $this->dqlQueryBuilder->setMaxResults($maxResults);
        }

        return $this->queryService->query($this->dqlQueryBuilder->build());
    }

    public function findOneByUsername(string $username): User
    {
        $dqlQuery = $this->dqlQueryBuilder->create(User::class)
            ->where('User.username = :username')
            ->setParameter('username', $username)
            ->build();

        return $this->queryService->query($dqlQuery)->getSingleResult();
    }

    public function findOneByEmail(string $email): User
    {
        $dqlQuery = $this->dqlQueryBuilder->create(User::class)
            ->where('User.email = :email')
            ->setParameter('email', $email)
            ->build();

        return $this->queryService->query($dqlQuery)->getSingleResult();
    }

    public function findOneById(UserId $id): User
    {
        $dqlQuery = $this->dqlQueryBuilder->create(User::class)
            ->where('User.id = :id')
            ->setParameter('id', $id)
            ->build();

        return $this->queryService->query($dqlQuery)->getSingleResult();
    }
}
