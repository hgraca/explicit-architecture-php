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

namespace Acme\App\Core\Component\Blog\Application\Query;

use Acme\App\Core\SharedKernel\Component\Blog\Domain\Post\Comment\CommentId;
use Acme\App\Core\SharedKernel\Component\User\Domain\User\UserId;

interface FindAuthorIdByCommentIdQueryInterface
{
    public function execute(CommentId $commentId): UserId;
}
