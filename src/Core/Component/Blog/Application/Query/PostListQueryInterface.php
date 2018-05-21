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

use Acme\App\Core\Port\Persistence\ResultCollectionInterface;
use Acme\App\Core\SharedKernel\Component\User\Domain\User\UserId;

interface PostListQueryInterface
{
    /**
     * Since this class only has one public method, it makes sense that it is designed as a callable, using the
     * magic method name `__invoke()`, instead of having a single public method called `execute()` which adds nothing
     * to code readability.
     * However, by using `__invoke()` we lose code completion, so in the end I prefer to use this `execute()` method.
     */
    public function execute(UserId $userId): ResultCollectionInterface;
}
