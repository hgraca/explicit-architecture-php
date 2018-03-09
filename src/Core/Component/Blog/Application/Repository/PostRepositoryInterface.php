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

namespace Acme\App\Core\Component\Blog\Application\Repository;

use Acme\App\Core\Component\Blog\Domain\Entity\Post;
use Acme\App\Core\Port\Persistence\QueryBuilderInterface;
use Acme\App\Core\Port\Persistence\ResultCollectionInterface;

interface PostRepositoryInterface
{
    public function find(int $id): Post;

    public function findBySlug(string $slug): Post;

    public function upsert(Post $entity): void;

    public function delete(Post $entity): void;

    /**
     * @return Post[]
     */
    public function findByAuthorOrderedByPublishDate(int $userId): ResultCollectionInterface;

    /**
     * @return Post[]
     */
    public function findLatest(): ResultCollectionInterface;

    /**
     * @return Post[]
     */
    public function findBySearchQuery(
        string $rawQuery,
        int $limit = QueryBuilderInterface::DEFAULT_MAX_RESULTS
    ): ResultCollectionInterface;
}
