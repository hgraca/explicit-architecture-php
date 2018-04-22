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

use Acme\App\Core\Port\Persistence\PersistenceServiceInterface;
use Acme\App\Core\Port\Persistence\QueryServiceInterface;
use Acme\App\Core\Port\Persistence\ResultCollection;
use Acme\App\Core\Port\Persistence\ResultCollectionInterface;
use Acme\App\Core\Port\Persistence\TransactionServiceInterface;
use Acme\PhpExtension\Helper\ClassHelper;
use Doctrine\DBAL\ConnectionException;
use Doctrine\ORM\EntityManagerInterface;

final class DqlPersistenceService implements QueryServiceInterface, PersistenceServiceInterface, TransactionServiceInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var bool
     */
    private $autoCommit;

    public function __construct(EntityManagerInterface $entityManager, bool $autoCommit = true)
    {
        $this->entityManager = $entityManager;
        $this->autoCommit = $autoCommit;
    }

    public function __invoke(DqlQuery $dqlQuery): ResultCollectionInterface
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        foreach ($dqlQuery->getFilters() as [$method, $arguments]) {
            $methodName = ClassHelper::extractCanonicalMethodName($method);
            $queryBuilder->$methodName(...$arguments);
        }

        $doctrineQuery = $queryBuilder->getQuery();
        $doctrineQuery->setHydrationMode($dqlQuery->getHydrationMode());

        return new ResultCollection($doctrineQuery->execute());
    }

    public function canHandle(): string
    {
        return DqlQuery::class;
    }

    public function upsert($entity): void
    {
        $this->entityManager->persist($entity);
    }

    public function delete($entity): void
    {
        $this->entityManager->remove($entity);
    }

    public function startTransaction(): void
    {
        if (!$this->autoCommit) {
            $this->entityManager->getConnection()->beginTransaction();
        }
    }

    public function commitChanges(): void
    {
        $this->entityManager->flush();
    }

    /**
     * @throws ConnectionException
     */
    public function finishTransaction(): void
    {
        $this->commitChanges();
        if (!$this->autoCommit && $this->entityManager->getConnection()->isTransactionActive()) {
            $this->entityManager->getConnection()->commit();
        }
    }

    /**
     * @throws ConnectionException
     */
    public function rollbackTransaction(): void
    {
        if (!$this->autoCommit && $this->entityManager->getConnection()->isTransactionActive()) {
            $this->entityManager->getConnection()->rollBack();
        }
        $this->entityManager->clear();
    }
}
