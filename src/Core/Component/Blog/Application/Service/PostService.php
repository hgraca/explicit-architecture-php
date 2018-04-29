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

use Acme\App\Core\Component\Blog\Application\Repository\PostRepositoryInterface;
use Acme\App\Core\Component\Blog\Domain\Entity\Post;
use Acme\App\Core\Component\User\Domain\Entity\User;

final class PostService
{
    /**
     * @var PostRepositoryInterface
     */
    private $postRepository;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function create(Post $post, User $user): void
    {
        $post->setAuthor($user);
        $post->regenerateSlug();

        $this->postRepository->upsert($post);
    }

    public function update(Post $post): void
    {
        $post->regenerateSlug();
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
