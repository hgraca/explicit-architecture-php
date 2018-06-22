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

namespace Acme\App\Presentation\Web\Core\Port\Auth;

use Acme\App\Core\Component\Blog\Domain\Post\Post;
use Acme\App\Core\SharedKernel\Component\User\Domain\User\UserId;

interface ResourceActionVoterInterface
{
    // Defining these constants is overkill for this simple application, but for real
    // applications, it's a recommended practice to avoid relying on "magic strings"
    public const SHOW = 'show';
    public const EDIT = 'edit';
    public const DELETE = 'delete';

    public function supports(string $attribute, $subject): bool;

    public function voteOnAttribute(string $attribute, Post $post, UserId $userId): bool;
}
