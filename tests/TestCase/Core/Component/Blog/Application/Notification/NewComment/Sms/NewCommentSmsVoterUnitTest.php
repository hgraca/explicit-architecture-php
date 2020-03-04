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

namespace Acme\App\Test\TestCase\Core\Component\Blog\Application\Notification\NewComment\Sms;

use Acme\App\Core\Component\Blog\Application\Notification\NewComment\Sms\NewCommentSmsVoter;
use Acme\App\Test\Framework\AbstractUnitTest;

/**
 * @internal
 *
 * @small
 */
final class NewCommentSmsVoterUnitTest extends AbstractUnitTest
{
    /**
     * @test
     */
    public function should_dispatch_sms(): void
    {
        $voter = new NewCommentSmsVoter();

        self::assertFalse($voter->shouldDispatchSms());
    }
}
