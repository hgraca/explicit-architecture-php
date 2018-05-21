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

namespace Acme\App\Test\TestCase\Core\Component\User\Application\Repository\DQL;

use Acme\App\Core\Component\User\Application\Repository\DQL\UserRepository;
use Acme\App\Core\Component\User\Domain\User\User;
use Acme\App\Core\Port\Persistence\DQL\DqlQueryBuilderInterface;
use Acme\App\Core\Port\Persistence\QueryServiceRouterInterface;
use Acme\App\Core\SharedKernel\Component\User\Domain\User\UserId;
use Acme\App\Infrastructure\Persistence\Doctrine\DqlPersistenceService;
use Acme\App\Test\Framework\AbstractIntegrationTest;

final class UserRepositoryIntegrationTest extends AbstractIntegrationTest
{
    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * @var DqlPersistenceService
     */
    private $persistenceService;

    /**
     * @var DqlQueryBuilderInterface
     */
    private $dqlQueryBuilder;

    /**
     * @var QueryServiceRouterInterface
     */
    private $queryService;

    public function setUp(): void
    {
        $this->repository = self::getService(UserRepository::class);
        $this->persistenceService = self::getService(DqlPersistenceService::class);
        $this->dqlQueryBuilder = self::getService(DqlQueryBuilderInterface::class);
        $this->queryService = self::getService(QueryServiceRouterInterface::class);
    }

    /**
     * @test
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function upsert_updates_entity(): void
    {
        $user = $this->findAUser();
        $userId = $user->getId();
        $newName = 'New Name';
        $user = $this->findById($userId);
        $user->setFullName($newName);
        $this->persistenceService->startTransaction();
        $this->repository->upsert($user);
        $this->persistenceService->finishTransaction();
        $this->clearDatabaseCache();

        $user = $this->findById($userId);

        self::assertSame($newName, $user->getFullName());
    }

    /**
     * @test
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function upsert_creates_entity(): void
    {
        $user = User::constructWithoutPassword(
            $username = 'username',
            $email = 'username@email.com',
            $fullName = 'User Name',
            User::ROLE_USER
        );
        $user->setPassword('plainpassword');

        $this->persistenceService->startTransaction();
        $this->repository->upsert($user);
        $this->persistenceService->finishTransaction();
        $userId = $user->getId();
        $this->clearDatabaseCache();

        $user = $this->findById($userId);

        self::assertSame($username, $user->getUsername());
        self::assertSame($email, $user->getEmail());
        self::assertSame($fullName, $user->getFullName());
        self::assertSame([User::ROLE_USER], $user->getRoles());
    }

    /**
     * @test
     * @expectedException \Acme\App\Core\Port\Persistence\Exception\EmptyQueryResultException
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function delete_removes_the_entity(): void
    {
        $user = $this->findAUser();
        $userId = $user->getId();
        $user = $this->findById($userId);

        $this->persistenceService->startTransaction();
        $this->repository->delete($user);
        $this->persistenceService->finishTransaction();

        $this->clearDatabaseCache();

        $this->findById($userId);
    }

    /**
     * @test
     */
    public function findAll_takes_DESC_ordering_into_account(): void
    {
        $allUsersOrderedDesc = $this->repository->findAll();
        /** @var User $previousUser */
        $previousUser = null;
        foreach ($allUsersOrderedDesc as $user) {
            if ($previousUser) {
                self::assertLessThanOrEqual($previousUser->getId()->toScalar(), $user->getId()->toScalar());
            }
            $previousUser = $user;
        }
    }

    /**
     * @test
     */
    public function findAll_takes_ASC_ordering_into_account(): void
    {
        $allUsersOrderedDesc = $this->repository->findAll(['id' => 'ASC']);
        /** @var User $previousUser */
        $previousUser = null;
        foreach ($allUsersOrderedDesc as $user) {
            if ($previousUser) {
                self::assertGreaterThanOrEqual($previousUser->getId()->toScalar(), $user->getId()->toScalar());
            }
            $previousUser = $user;
        }
    }

    /**
     * @test
     */
    public function findAll_takes_max_results_into_account(): void
    {
        $userList = $this->repository->findAll(['id' => 'DESC'], 1);

        self::assertSame(1, $userList->count());
    }

    /**
     * @test
     */
    public function findOneByUsername(): void
    {
        $user = $this->findAUser();
        $userId = $user->getId();
        $username = $this->findById($userId)->getUsername();

        $this->clearDatabaseCache();

        $user = $this->repository->findOneByUsername($username);

        self::assertTrue($userId->equals($user->getId()));
    }

    /**
     * @test
     */
    public function findOneByEmail(): void
    {
        $user = $this->findAUser();
        $userId = $user->getId();
        $email = $this->findById($userId)->getEmail();

        $this->clearDatabaseCache();

        $user = $this->repository->findOneByEmail($email);

        self::assertTrue($userId->equals($user->getId()));
    }

    /**
     * @test
     */
    public function findOneById(): void
    {
        $user = $this->findAUser();
        $userId = $user->getId();

        $user = $this->repository->findOneById($userId);

        self::assertTrue($userId->equals($user->getId()));
    }

    private function findById(UserId $id): User
    {
        $dqlQuery = $this->dqlQueryBuilder->create(User::class)
            ->where('User.id = :id')
            ->setParameter('id', $id)
            ->build();

        return $this->queryService->query($dqlQuery)->getSingleResult();
    }

    private function findAUser(): User
    {
        $dqlQuery = $this->dqlQueryBuilder->create(User::class)->setMaxResults(1)->build();

        return $this->queryService->query($dqlQuery)->getSingleResult();
    }
}
