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

use Acme\App\Core\Component\Blog\Domain\Entity\Post;
use Acme\App\Core\Component\User\Domain\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;

final class PostService
{
    /**
     * @var ObjectManager
     */
    private $entityManager;

    public function __construct(ObjectManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(Post $post, User $user): void
    {
        $post->setAuthor($user);
        $post->regenerateSlug();

        // TODO replace by the repository
        $this->entityManager->persist($post);
        // Flushing is when doctrine writes all staged changes, to the DB
        // so we should do this only once in a request, and only if the use case command was successful
        $this->entityManager->flush(); // if we would use a command bus, we would do this in a middleware
    }

    public function update(Post $post): void
    {
        $post->regenerateSlug();
        // TODO replace by the repository
        // Flushing is when doctrine writes all staged changes, to the DB
        // so we should do this only once in a request, and only if the use case command was successful
        $this->entityManager->flush(); // if we would use a command bus, we would do this in a middleware
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
        // Delete the tags associated with this blog post. This is done automatically
        // by Doctrine, except for SQLite (the database used in this application)
        // because foreign key support is not enabled by default in SQLite
        $post->getTags()->clear();

        // TODO replace by the repository
        $this->entityManager->remove($post);
        // Flushing is when doctrine writes all staged changes, to the DB
        // so we should do this only once in a request, and only if the use case command was successful
        $this->entityManager->flush(); // if we would use a command bus, we would do this in a middleware
    }
}
