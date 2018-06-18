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

namespace Acme\App\Core\SharedKernel\Component\Blog\Domain\Post\Comment;

use Acme\PhpExtension\Identity\AbstractUuidId;

/**
 * This ID needs to be in the SharedKernel because the CommentCreatedEvent depends on it, so when if it is sent over a
 * queue to a separate service, it would need this ID there to be hydrated (be it in a PHP class format or in a
 * descriptive format).
 */
final class CommentId extends AbstractUuidId
{
}
