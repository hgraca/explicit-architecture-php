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

use Acme\App\Core\Component\Blog\Domain\Entity\Comment;
use Acme\App\Core\Component\Blog\Domain\Entity\Post;
use Acme\App\Core\Component\User\Domain\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;

final class CommentService
{
    /**
     * @var ObjectManager
     */
    private $entityManager;

    public function __construct(ObjectManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(Post $post, Comment $comment, User $author): void
    {
        $comment->setAuthor($author);
        $post->addComment($comment);

        // TODO replace by the repository
        $this->entityManager->persist($comment);
        // Flushing is when doctrine writes all staged changes, to the DB
        // so we should do this only once in a request, and only if the use case command was successful
        $this->entityManager->flush(); // if we would use a command bus, we would do this in a middleware
    }
}
