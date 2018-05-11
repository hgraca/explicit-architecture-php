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

namespace Acme\App\Core\Component\Blog\Application\Service;

use Acme\App\Core\Component\Blog\Application\Query\FindHighestPostSlugSuffixQueryInterface;
use Acme\App\Core\Component\Blog\Application\Query\PostSlugExistsQueryInterface;
use Acme\App\Core\Component\Blog\Application\Repository\PostRepositoryInterface;
use Acme\App\Core\Component\Blog\Domain\Post\Post;
use Acme\App\Core\Component\User\Domain\User\User;
use Acme\App\Core\Port\Lock\LockManagerInterface;

final class PostService
{
    public const SLUG_LOCK_PREFIX = 'slug_creation-';

    /**
     * @var PostRepositoryInterface
     */
    private $postRepository;

    /**
     * @var PostSlugExistsQueryInterface
     */
    private $postSlugExistsQuery;

    /**
     * @var FindHighestPostSlugSuffixQueryInterface
     */
    private $findHighestPostSlugSuffixQuery;

    /**
     * @var LockManagerInterface
     */
    private $lockManager;

    public function __construct(
        PostRepositoryInterface $postRepository,
        PostSlugExistsQueryInterface $postSlugExistsQuery,
        FindHighestPostSlugSuffixQueryInterface $findHighestPostSlugSuffixQuery,
        LockManagerInterface $lockManager
    ) {
        $this->postRepository = $postRepository;
        $this->postSlugExistsQuery = $postSlugExistsQuery;
        $this->findHighestPostSlugSuffixQuery = $findHighestPostSlugSuffixQuery;
        $this->lockManager = $lockManager;
    }

    public function create(Post $post, User $user): void
    {
        $post->setAuthor($user);

        // We acquire a lock on the creation of a slug here, to prevent race conditions while generating the sequential
        //  ID used to make the slug unique.
        // We use the slug as part of the lock name so that we only block the post creation requests that try to
        //  create a post with the same slug.
        $this->lockManager->acquire(self::SLUG_LOCK_PREFIX . $post->getSlug());

        if ($this->postSlugExistsQuery->execute($post->getSlug())) {
            $highestPostSlugSuffix = $this->findHighestPostSlugSuffixQuery->execute($post->getSlug());
            $post->postfixSlug((string) ++$highestPostSlugSuffix);
        }

        $this->postRepository->upsert($post);
    }

    /**
     * This method is quite simple, so it might seem useless to have it. However, it is debatable if we should have it
     * or not, and several things come to my mind:
     *  On one hand, this use case can be triggered from several locations and if/when we need to change it
     *      (ie to make it a soft delete instead of a hard delete or to send out an event), we would only need to
     *      change it here.
     *  On the other hand, being so simple, we could just use the repository directly in the controllers,
     *      and move the logic to a service only if/when the logic becomes more complex.
     *  Yet on another hand, if we use a command bus, any data changes must be done through a command/handler
     *      (so they can be queued and possibly retried) and such a simple operation is no exception.
     *
     * For the sake of the previous explanation, I decided to leave it here.
     */
    public function delete(Post $post): void
    {
        $this->postRepository->delete($post);
    }
}
