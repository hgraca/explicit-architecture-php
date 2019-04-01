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

use Acme\App\Core\Component\Blog\Domain\Post\Post;
use Acme\App\Core\Component\Blog\Domain\Post\PostId;
use Acme\App\Core\Port\Persistence\ResultCollectionInterface;
use Acme\App\Core\SharedKernel\Component\User\Domain\User\UserId;

interface PostRepositoryInterface
{
    /**
     * @return Post[]
     */
    public function findAll(array $orderByList = ['id' => 'DESC'], int $maxResults = null): ResultCollectionInterface;

    /**
     * @return Post[]
     */
    public function findAllByUserId(
        UserId $userId,
        array $orderByList = ['publishedAt' => 'DESC'],
        int $maxResults = null
    ): ResultCollectionInterface;

    public function find(PostId $id): Post;

    public function findBySlug(string $slug): Post;

    public function add(Post $entity): void;

    public function remove(Post $entity): void;
}
