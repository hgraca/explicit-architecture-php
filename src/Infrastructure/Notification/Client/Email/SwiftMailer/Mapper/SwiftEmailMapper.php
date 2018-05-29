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

namespace Acme\App\Infrastructure\Notification\Client\Email\SwiftMailer\Mapper;

use Acme\App\Core\Port\Notification\Client\Email\Email;
use Swift_Attachment;
use Swift_Message;
use Swift_Mime_SimpleMessage;

/**
 * @author Marijn Koesen
 * @author Herberto Graca <herberto.graca@gmail.com>
 */
class SwiftEmailMapper implements EmailMapper
{
    public function map(Email $message): Swift_Mime_SimpleMessage
    {
        // Symfony uses a library called SwiftMailer to send emails. That's why
        // email messages are created instantiating a Swift_Message class.
        // See https://symfony.com/doc/current/email.html#sending-emails
        $swiftMessage = new Swift_Message($message->getSubject());

        $swiftMessage->setFrom($message->getFrom()->getEmail(), $message->getFrom()->getName());

        foreach ($message->getTo() as $toReceiver) {
            $swiftMessage->addTo($toReceiver->getEmail(), $toReceiver->getName());
        }
        foreach ($message->getCc() as $ccReceiver) {
            $swiftMessage->addCc($ccReceiver->getEmail(), $ccReceiver->getName());
        }
        foreach ($message->getBcc() as $bccReceiver) {
            $swiftMessage->addBcc($bccReceiver->getEmail(), $bccReceiver->getName());
        }

        foreach ($message->getParts() as $part) {
            $swiftMessage->addPart($part->getContent(), $part->getContentType(), $part->getCharset());
        }

        foreach ($message->getHeaders() as $header) {
            $swiftMessage->getHeaders()->addTextHeader($header->getName(), $header->getValue());
        }

        foreach ($message->getAttachments() as $attachment) {
            $swiftMessage->attach(new Swift_Attachment(
                $attachment->getContent(), $attachment->getFileName(), $attachment->getContentType()
            ));
        }

        return $swiftMessage;
    }
}
