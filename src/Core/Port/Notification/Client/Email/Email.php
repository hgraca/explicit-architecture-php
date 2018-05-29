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

namespace Acme\App\Core\Port\Notification\Client\Email;

use Acme\App\Core\Port\Notification\Client\Email\Exception\EmailAddressNotFoundException;
use Acme\App\Core\Port\Notification\Client\Email\Exception\EmailPartAlreadyProvidedException;

/**
 * @author Herberto Graca <herberto.graca@gmail.com>
 * @author Jeroen Van Den Heuvel
 * @author Marijn Koesen
 * @author Rodrigo Prestes
 * @author Ruud Van Der Weiijde
 */
class Email
{
    /**
     * @var string
     */
    private $subject;

    /**
     * @var EmailPart[]
     */
    private $parts = [];

    /**
     * @var EmailAddress|null
     */
    private $from;

    /**
     * @var EmailAddress[]
     */
    private $to = [];

    /**
     * @var EmailAddress[]
     */
    private $cc = [];

    /**
     * @var EmailAddress[]
     */
    private $bcc = [];

    /**
     * @var EmailHeader[]
     */
    private $headers = [];

    /**
     * @var bool
     */
    private $trackMessageOpening;

    /**
     * @var bool
     */
    private $trackClicks;

    /**
     * With this, an email provider like Mailchimp can track how many emails were open, etc.
     *
     * @var string
     */
    private $trackingCampaign = '';

    /**
     * @var array
     */
    private $tags = [];

    /**
     * @var EmailAttachment[]
     */
    private $attachments = [];

    public function __construct(string $subject, EmailAddress $from)
    {
        $this->subject = $subject;
        $this->from = $from;

        $this->trackMessageOpening = false;
        $this->trackClicks = false;
    }

    public function setBodyHtml(string $content, string $charset = null): void
    {
        $this->addPart($content, MimeType::MIME_TYPE_HTML, $charset);
    }

    public function setBodyText(string $content, string $charset = null): void
    {
        $this->addPart($content, MimeType::MIME_TYPE_PLAIN, $charset);
    }

    public function getPlainTextPart(): ?EmailPart
    {
        foreach ($this->parts as $part) {
            if ($part->getContentType() === MimeType::MIME_TYPE_PLAIN) {
                return $part;
            }
        }

        return null;
    }

    public function getHtmlPart(): ?EmailPart
    {
        foreach ($this->parts as $part) {
            if ($part->getContentType() === MimeType::MIME_TYPE_HTML) {
                return $part;
            }
        }

        return null;
    }

    /**
     * @throws EmailPartAlreadyProvidedException
     */
    protected function addPart(string $content, string $contentType = null, string $charset = null): void
    {
        foreach ($this->getParts() as $part) {
            if ($part->getContentType() === $contentType) {
                throw new EmailPartAlreadyProvidedException(sprintf('Message already contains part [%s] and it can only be provided once. Therefore the following content can\'t be added to the message [%s]. ', $contentType, $contentType));
            }
        }

        $this->parts[] = new EmailPart($content, $contentType, $charset);
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return EmailPart[]
     */
    public function getParts(): array
    {
        return $this->parts;
    }

    public function addTo(EmailAddress $emailAddress): void
    {
        $this->to[] = $emailAddress;
    }

    public function addCc(EmailAddress $emailAddress): void
    {
        $this->cc[] = $emailAddress;
    }

    public function addBcc(EmailAddress $emailAddress): void
    {
        $this->bcc[] = $emailAddress;
    }

    public function getFrom(): EmailAddress
    {
        return $this->from;
    }

    /**
     * @return EmailAddress[]
     */
    public function getTo(): array
    {
        return $this->to;
    }

    /**
     * @return EmailAddress[]
     */
    public function getCc(): array
    {
        return $this->cc;
    }

    /**
     * @return EmailAddress[]
     */
    public function getBcc(): array
    {
        return $this->bcc;
    }

    public function addHeader(string $name, string $value = ''): void
    {
        $this->headers[] = new EmailHeader($name, $value);
    }

    /**
     * @return EmailHeader[]
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setTrackMessageOpening(bool $state): void
    {
        $this->trackMessageOpening = $state;
    }

    public function setTrackClicks(bool $state): void
    {
        $this->trackClicks = $state;
    }

    public function setTrackingCampaign(string $trackingCampaign): void
    {
        $this->trackingCampaign = $trackingCampaign;
    }

    public function getTrackingCampaign(): string
    {
        return $this->trackingCampaign;
    }

    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function shouldTrackClicks(): bool
    {
        return $this->trackClicks;
    }

    public function shouldTrackMessageOpening(): bool
    {
        return $this->trackMessageOpening;
    }

    public function getFirstTo(): EmailAddress
    {
        $tos = $this->getTo();

        return array_shift($tos);
    }

    /**
     * @throws EmailAddressNotFoundException
     */
    public function getFirstToMatchingRegex(string $regex): EmailAddress
    {
        $tos = $this->getTo();

        foreach ($tos as $to) {
            if (preg_match($regex, $to->getEmail())) {
                return $to;
            }
        }

        throw new EmailAddressNotFoundException($regex);
    }

    public function addAttachment(EmailAttachmentInterface $attachment): void
    {
        $this->attachments[] = $attachment;
    }

    /**
     * @return EmailAttachmentInterface[]
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    public function hasAttachments(): bool
    {
        return \count($this->attachments) > 0;
    }
}
