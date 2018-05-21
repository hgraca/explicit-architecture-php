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

namespace Acme\App\Core\Component\Blog\Application\Repository\DQL;

use Acme\App\Core\Component\Blog\Application\Repository\PostRepositoryInterface;
use Acme\App\Core\Component\Blog\Domain\Post\Post;
use Acme\App\Core\Component\Blog\Domain\Post\PostId;
use Acme\App\Core\Component\User\Domain\User\UserId;
use Acme\App\Core\Port\Persistence\DQL\DqlQueryBuilderInterface;
use Acme\App\Core\Port\Persistence\PersistenceServiceInterface;
use Acme\App\Core\Port\Persistence\QueryServiceRouterInterface;
use Acme\App\Core\Port\Persistence\ResultCollectionInterface;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 * @author Herberto Graca <herberto.graca@gmail.com>
 */
class PostRepository implements PostRepositoryInterface
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

    public function find(PostId $id): Post
    {
        $dqlQuery = $this->dqlQueryBuilder->create(Post::class)
            ->where('Post.id = :id')
            ->setParameter('id', $id)
            ->build();

        return $this->queryService->query($dqlQuery)->getSingleResult();
    }

    public function findBySlug(string $slug): Post
    {
        $dqlQuery = $this->dqlQueryBuilder->create(Post::class)
            ->where('Post.slug = :slug')
            ->setParameter('slug', $slug)
            ->build();

        return $this->queryService->query($dqlQuery)->getSingleResult();
    }

    public function upsert(Post $entity): void
    {
        $this->persistenceService->upsert($entity);
    }

    public function delete(Post $entity): void
    {
        // Delete the tags associated with this blog post. This is done automatically
        // by Doctrine, except for SQLite (the database used in this application)
        // because foreign key support is not enabled by default in SQLite
        $entity->clearTags();

        $this->persistenceService->delete($entity);
    }

    /**
     * @return Post[]
     */
    public function findByAuthorOrderedByPublishDate(UserId $userId): ResultCollectionInterface
    {
        $dqlQuery = $this->dqlQueryBuilder->create(Post::class)
            ->where('Post.authorId = :userId')
            ->orderBy('Post.publishedAt', 'DESC')
            ->setParameter('userId', $userId)
            ->build();

        return $this->queryService->query($dqlQuery);
    }
}
