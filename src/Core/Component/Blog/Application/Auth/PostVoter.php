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

namespace Acme\App\Core\Component\Blog\Application\Auth;

use Acme\App\Core\Component\Blog\Domain\Post\Post;
use Acme\App\Core\Port\Auth\Authorization\ResourceActionVoterInterface;
use Acme\App\Core\SharedKernel\Component\User\Domain\User\UserId;

final class PostVoter implements ResourceActionVoterInterface
{
    /**
     * @param Post $subject
     */
    public function supports(string $attribute, $subject): bool
    {
        // this voter is only executed for three specific permissions on Post objects
        return $subject instanceof Post && \in_array($attribute, [self::SHOW, self::EDIT, self::DELETE], true);
    }

    public function voteOnAttribute(string $attribute, Post $post, UserId $userId): bool
    {
        // the logic of this voter is pretty simple: if the logged user is the
        // author of the given blog post, grant permission; otherwise, deny it.
        // (the supports() method guarantees that $post is a Post object)
        return $userId->equals($post->getAuthorId());
    }
}
