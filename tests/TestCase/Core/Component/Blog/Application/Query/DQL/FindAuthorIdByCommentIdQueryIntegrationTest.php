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

namespace Acme\App\Test\TestCase\Core\Component\Blog\Application\Query\DQL;

use Acme\App\Core\Component\Blog\Application\Query\FindAuthorIdByCommentIdQueryInterface;
use Acme\App\Core\Component\Blog\Domain\Post\Comment\Comment;
use Acme\App\Core\Port\Persistence\DQL\DqlQueryBuilderInterface;
use Acme\App\Core\Port\Persistence\QueryServiceRouterInterface;
use Acme\App\Test\Framework\AbstractIntegrationTest;

/**
 * @medium
 *
 * @internal
 */
final class FindAuthorIdByCommentIdQueryIntegrationTest extends AbstractIntegrationTest
{
    /**
     * @var DqlQueryBuilderInterface
     */
    private $dqlQueryBuilder;

    /**
     * @var QueryServiceRouterInterface
     */
    private $queryService;

    protected function setUp(): void
    {
        $this->dqlQueryBuilder = self::getService(DqlQueryBuilderInterface::class);
        $this->queryService = self::getService(QueryServiceRouterInterface::class);
    }

    /**
     * @test
     */
    public function execute(): void
    {
        $comment = $this->findAComment();
        $commentId = $comment->getId();
        $this->clearDatabaseCache();

        self::assertEquals($comment->getAuthorId(), $this->getQuery()->execute($commentId));
    }

    private function getQuery(): FindAuthorIdByCommentIdQueryInterface
    {
        return self::getService(FindAuthorIdByCommentIdQueryInterface::class);
    }

    private function findAComment(): Comment
    {
        $dqlQuery = $this->dqlQueryBuilder->create(Comment::class)->setMaxResults(1)->build();

        return $this->queryService->query($dqlQuery)->getSingleResult();
    }
}
