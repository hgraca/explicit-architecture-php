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

namespace Acme\App\Test\TestCase\Infrastructure\Notification\Client\Email\SwiftMailer;

use Acme\App\Core\Port\Notification\Client\Email\Email;
use Acme\App\Core\Port\Notification\Client\Email\EmailAddress;
use Acme\App\Infrastructure\Notification\Client\Email\SwiftMailer\Mapper\EmailMapper;
use Acme\App\Infrastructure\Notification\Client\Email\SwiftMailer\SwiftMailer;
use Acme\App\Test\Framework\AbstractUnitTest;
use Mockery;
use Mockery\MockInterface;
use Swift_Mailer;
use Swift_Mime_SimpleMessage;

/**
 * @author Herberto Graca <herberto.graca@gmail.com>
 * @author Kasper Agg
 * @author Marijn Koesen
 *
 * @small
 *
 * @internal
 */
final class SwiftMailerUnitTest extends AbstractUnitTest
{
    /**
     * @test
     */
    public function send(): void
    {
        $email = new Email('some subject', new EmailAddress('some.one@foo.com'));
        $email->addTo(new EmailAddress('some.one.else@bar.com'));

        $swiftMessage = $this->getMockedSwift_Mime_Message();
        $swiftMailer = $this->getMockedSwift_Mailer();
        $swiftMapper = $this->getMockedEmailMapper();

        $swiftMapper
            ->shouldReceive('map')
            ->once()
            ->with($email)
            ->andReturn($swiftMessage);

        $swiftMailer
            ->shouldReceive('send')
            ->once()
            ->with($swiftMessage);

        $mailer = new SwiftMailer($swiftMailer, $swiftMapper);
        $mailer->send($email);
    }

    /**
     * @return MockInterface|Swift_Mime_SimpleMessage
     */
    private function getMockedSwift_Mime_Message()
    {
        return Mockery::mock(Swift_Mime_SimpleMessage::class);
    }

    /**
     * @return MockInterface|Swift_Mailer
     */
    private function getMockedSwift_Mailer()
    {
        return Mockery::mock(Swift_Mailer::class);
    }

    /**
     * @return MockInterface|EmailMapper
     */
    private function getMockedEmailMapper()
    {
        return Mockery::mock(EmailMapper::class);
    }
}
