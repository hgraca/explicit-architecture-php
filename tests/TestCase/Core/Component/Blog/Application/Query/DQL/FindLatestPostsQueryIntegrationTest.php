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

use Acme\App\Core\Component\Blog\Application\Query\DQL\FindLatestPostsQuery;
use Acme\App\Core\Component\Blog\Application\Query\FindLatestPostsQueryInterface;
use Acme\App\Core\Component\Blog\Application\Query\PostWithAuthorAndTagsDto;
use Acme\App\Core\Component\Blog\Application\Repository\DQL\PostRepository;
use Acme\App\Core\Component\Blog\Domain\Post\Post;
use Acme\App\Core\Port\Persistence\DQL\DqlQueryBuilderInterface;
use Acme\App\Core\Port\Persistence\QueryServiceRouterInterface;
use Acme\App\Infrastructure\Persistence\Doctrine\DqlPersistenceService;
use Acme\App\Test\Framework\AbstractIntegrationTest;
use Acme\PhpExtension\DateTime\DateTimeGenerator;
use DateTimeImmutable;

/**
 * @medium
 *
 * @internal
 */
final class FindLatestPostsQueryIntegrationTest extends AbstractIntegrationTest
{
    /**
     * @var PostRepository
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

    protected function setUp(): void
    {
        DateTimeGenerator::overrideDefaultGenerator(function () {
            return new DateTimeImmutable('now + 1 hour');
        });
        $this->repository = self::getService(PostRepository::class);
        $this->persistenceService = self::getService(DqlPersistenceService::class);
        $this->dqlQueryBuilder = self::getService(DqlQueryBuilderInterface::class);
        $this->queryService = self::getService(QueryServiceRouterInterface::class);
    }

    /**
     * @test
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function execute(): void
    {
        $auxiliaryPost = $this->findAPost();

        $post = new Post();
        $post->setAuthorId($auxiliaryPost->getAuthorId());
        $post->setContent($content = 'some new content');
        $post->setTitle($title = 'a title');
        $post->setSummary($summary = 'a summary');

        $this->persistenceService->startTransaction();
        $this->repository->add($post);
        $this->persistenceService->finishTransaction();

        $postList = $this->getFindLatestPostsQuery()->execute();

        /** @var PostWithAuthorAndTagsDto $previousPost */
        $previousPost = null;
        foreach ($postList as $key => $aPost) {
            if ($key === 0) {
                self::assertSame($post->getSlug(), $aPost->getSlug());
            }
            if ($previousPost) {
                self::assertLessThanOrEqual($previousPost->getPublishedAt(), $aPost->getPublishedAt());
            }
            $previousPost = $aPost;
        }
    }

    private function getFindLatestPostsQuery(): FindLatestPostsQueryInterface
    {
        return self::getService(FindLatestPostsQuery::class);
    }

    private function findAPost(): Post
    {
        $dqlQuery = $this->dqlQueryBuilder->create(Post::class)->setMaxResults(1)->build();

        return $this->queryService->query($dqlQuery)->getSingleResult();
    }
}
