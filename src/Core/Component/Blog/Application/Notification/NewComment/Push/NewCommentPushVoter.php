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

namespace Acme\App\Core\Component\Blog\Application\Notification\NewComment\Push;

/**
 * This class is here just as an example of a notification voter, because voters are not mandatory
 */
final class NewCommentPushVoter
{
    public function shouldDispatchPush(): bool
    {
        return false;
    }
}
