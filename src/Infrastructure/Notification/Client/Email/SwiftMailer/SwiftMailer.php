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

namespace Acme\App\Infrastructure\Notification\Client\Email\SwiftMailer;

use Acme\App\Core\Port\Notification\Client\Email\Email;
use Acme\App\Core\Port\Notification\Client\Email\EmailerInterface;
use Acme\App\Infrastructure\Notification\Client\Email\SwiftMailer\Mapper\EmailMapper;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Swift_Mailer;

/**
 * @author Marijn Koesen
 * @author Kasper Agg
 * @author Herberto Graca <herberto.graca@gmail.com>
 */
class SwiftMailer implements EmailerInterface
{
    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @var EmailMapper
     */
    private $swiftEmailMapper;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(Swift_Mailer $mailer, EmailMapper $swiftEmailMapper, LoggerInterface $logger = null)
    {
        $this->mailer = $mailer;
        $this->swiftEmailMapper = $swiftEmailMapper;
        $this->logger = $logger ?? new NullLogger();
    }

    public function send(Email $email): void
    {
        // Symfony uses a library called SwiftMailer to send emails. That's why
        // we need to map from our Email class to a Swift_Message class.
        // See https://symfony.com/doc/current/email.html#sending-emails
        $swiftMessage = $this->swiftEmailMapper->map($email);

        // In config/packages/dev/swiftmailer.yaml the 'disable_delivery' option is set to 'true'.
        // That's why in the development environment you won't actually receive any email.
        // However, you can inspect the contents of those unsent emails using the debug toolbar.
        // See https://symfony.com/doc/current/email/dev_environment.html#viewing-from-the-web-debug-toolbar
        $this->mailer->send($swiftMessage);

        $this->logger->info(
            'Sent email with subject "' . $email->getSubject() . '" to ' . implode(', ', $email->getTo())
        );
    }
}
