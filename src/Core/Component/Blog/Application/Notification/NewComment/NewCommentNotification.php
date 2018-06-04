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

namespace Acme\App\Core\Component\Blog\Application\Notification\NewComment;

use Acme\App\Core\Component\Blog\Domain\Post\Comment\CommentId;
use Acme\App\Core\Component\Blog\Domain\Post\PostId;
use Acme\App\Core\Port\Notification\Client\Email\EmailAddress;
use Acme\App\Core\Port\Notification\NotificationInterface;
use Acme\App\Core\SharedKernel\Component\User\Domain\User\UserId;

final class NewCommentNotification implements NotificationInterface
{
    /**
     * @var UserId
     */
    private $postAuthorId;

    /**
     * @var string
     */
    private $postAuthorMobile;

    /**
     * @var CommentId
     */
    private $commentId;

    /**
     * @var EmailAddress
     */
    private $emailAddress;

    /**
     * @var PostId
     */
    private $postId;

    /**
     * @var string
     */
    private $postTitle;

    /**
     * @var string
     */
    private $postSlug;

    public function __construct(
        UserId $postAuthorId,
        string $postAuthorMobile,
        CommentId $commentId,
        EmailAddress $emailAddress,
        PostId $postId,
        string $postTitle,
        string $postSlug
    ) {
        $this->postAuthorId = $postAuthorId;
        $this->postAuthorMobile = $postAuthorMobile;
        $this->commentId = $commentId;
        $this->emailAddress = $emailAddress;
        $this->postId = $postId;
        $this->postTitle = $postTitle;
        $this->postSlug = $postSlug;
    }

    public function getDestinationUserId(): UserId
    {
        return $this->postAuthorId;
    }

    public function getPostAuthorMobile(): string
    {
        return $this->postAuthorMobile;
    }

    public function getCommentId(): CommentId
    {
        return $this->commentId;
    }

    public function getPostAuthorEmail(): EmailAddress
    {
        return $this->emailAddress;
    }

    public function getPostId(): PostId
    {
        return $this->postId;
    }

    public function getPostTitle(): string
    {
        return $this->postTitle;
    }

    public function getPostSlug(): string
    {
        return $this->postSlug;
    }
}
