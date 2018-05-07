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

use Acme\App\Core\Component\Blog\Application\Repository\CommentRepositoryInterface;
use Acme\App\Core\Component\Blog\Domain\Entity\Comment;
use Acme\App\Core\Component\Blog\Domain\Entity\Post;
use Acme\App\Core\Component\User\Domain\Entity\User;
use Acme\App\Core\Port\EventDispatcher\EventDispatcherInterface;
use Acme\App\Core\SharedKernel\Component\Blog\Application\Event\CommentCreatedEvent;

final class CommentService
{
    /**
     * @var CommentRepositoryInterface
     */
    private $commentRepository;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        CommentRepositoryInterface $commentRepository,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->commentRepository = $commentRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function create(Post $post, Comment $comment, User $author): void
    {
        $comment->setAuthor($author);
        $post->addComment($comment);

        $this->commentRepository->upsert($comment);

        // When triggering an event, you can optionally pass some information.
        // For simple applications, use the GenericEvent object provided by Symfony
        // to pass some PHP variables. For more complex applications, define your
        // own event object classes.
        // See https://symfony.com/doc/current/components/event_dispatcher/generic_event.html
        $event = new CommentCreatedEvent($comment->getId());

        // When an event is dispatched, Symfony notifies it to all the listeners
        // and subscribers registered to it. Listeners can modify the information
        // passed in the event and they can even modify the execution flow, so
        // there's no guarantee that the rest of this method will be executed.
        // See https://symfony.com/doc/current/components/event_dispatcher.html
        $this->eventDispatcher->dispatch($event);
    }
}
