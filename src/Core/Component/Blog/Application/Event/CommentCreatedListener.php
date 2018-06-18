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

namespace Acme\App\Core\Component\Blog\Application\Event;

use Acme\App\Core\Component\Blog\Application\Notification\NewComment\NewCommentNotification;
use Acme\App\Core\Component\Blog\Application\Query\PostQueryInterface;
use Acme\App\Core\Component\Blog\Application\Query\PostWithAuthorDto;
use Acme\App\Core\Port\Notification\Client\Email\EmailAddress;
use Acme\App\Core\Port\Notification\NotificationServiceInterface;
use Acme\App\Core\SharedKernel\Component\Blog\Application\Event\CommentCreatedEvent;
use Acme\App\Core\SharedKernel\Component\Blog\Domain\Post\Comment\CommentId;

/**
 * Listens to the CommentCreatedEvent and triggers all the logic associated with it, in this component.
 *
 * @author Oleg Voronkovich <oleg-voronkovich@yandex.ru>
 * @author Herberto Graca <herberto.graca@gmail.com>
 */
class CommentCreatedListener
{
    /**
     * @var PostQueryInterface
     */
    private $postQuery;

    /**
     * @var NotificationServiceInterface
     */
    private $notificationService;

    public function __construct(
        PostQueryInterface $postQuery,
        NotificationServiceInterface $notificationService
    ) {
        $this->postQuery = $postQuery;
        $this->notificationService = $notificationService;
    }

    public function notifyPostAuthorAboutNewComment(CommentCreatedEvent $event): void
    {
        $commentId = $event->getCommentId();
        $postDto = $this->getPostDto($commentId);

        $this->notificationService->notify(
            new NewCommentNotification(
                $postDto->getAuthorId(),
                $postDto->getAuthorMobile(),
                $commentId,
                new EmailAddress($postDto->getAuthorEmail(), $postDto->getAuthorFullName()),
                $postDto->getId(),
                $postDto->getTitle(),
                $postDto->getSlug()
            )
        );
    }

    private function getPostDto(CommentId $commentId): PostWithAuthorDto
    {
        return $this->postQuery
            ->includeAuthor()
            ->execute($commentId)
            ->hydrateSingleResultAs(PostWithAuthorDto::class);
    }
}
