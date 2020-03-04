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

namespace Acme\App\Test\TestCase\Core\Component\Blog\Application\Notification\NewComment\Email;

use Acme\App\Core\Component\Blog\Application\Notification\NewComment\Email\NewCommentEmailVoter;
use Acme\App\Test\Framework\AbstractUnitTest;

/**
 * @internal
 *
 * @small
 */
final class NewCommentEmailVoterUnitTest extends AbstractUnitTest
{
    /**
     * @test
     */
    public function should_dispatch_email(): void
    {
        $voter = new NewCommentEmailVoter();

        self::assertTrue($voter->shouldDispatchEmail());
    }
}
