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

namespace Acme\App\Core\SharedKernel\Component\Blog\Application\Event;

use Acme\App\Core\Port\EventDispatcher\EventInterface;
use Acme\App\Core\SharedKernel\Component\Blog\Domain\Post\Comment\CommentId;

/**
 * This is just a DTO, it only has getters, theres no logic to test, so we ignore it for code coverage purposes.
 *
 * @codeCoverageIgnore
 */
final class CommentCreatedEvent implements EventInterface
{
    /**
     * @var CommentId
     */
    private $commentId;

    public function __construct(CommentId $commentId)
    {
        $this->commentId = $commentId;
    }

    public function getCommentId(): CommentId
    {
        return $this->commentId;
    }
}
