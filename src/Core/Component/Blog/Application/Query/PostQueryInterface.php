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

use Acme\App\Core\Component\Blog\Domain\Post\PostId;
use Acme\App\Core\Port\Persistence\ResultCollectionInterface;
use Acme\App\Core\SharedKernel\Component\Blog\Domain\Post\Comment\CommentId;

interface PostQueryInterface
{
    public function includeTags(): self;

    public function includeComments(): self;

    public function includeCommentsAuthor(): self;

    public function includeAuthor(): self;

    /**
     * @param PostId|CommentId $id
     */
    public function execute($id): ResultCollectionInterface;
}
