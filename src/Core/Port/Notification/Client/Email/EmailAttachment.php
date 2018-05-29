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

use Acme\App\Core\Port\Notification\Client\Email\Exception\EmailAttachmentException;
use Serializable;

/**
 * @author Herberto Graca <herberto.graca@gmail.com>
 * @author Ruud Van Der Weiijde
 */
class EmailAttachment implements EmailAttachmentInterface, Serializable
{
    const ERROR_INVALID_FILE_NAME = 'Invalid file name provided.';
    const ERROR_INVALID_CONTENT = 'Invalid content provided.';
    const ERROR_INVALID_CONTENT_TYPE = 'Invalid content type provided.';

    /**
     * @var string
     */
    private $fileName;

    /**
     * @var string
     */
    private $contentType;

    /**
     * @var string
     */
    private $content;

    /**
     * @throws EmailAttachmentException
     */
    public function __construct(string $fileName, string $contentType, string $content)
    {
        $this->setFileName($fileName);
        $this->setContentType($contentType);
        $this->setContent($content);
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @throws \Acme\App\Core\Port\Notification\Client\Email\Exception\EmailAttachmentException
     */
    protected function setFileName(string $fileName): void
    {
        if (!\is_string($fileName) || empty($fileName)) {
            throw new EmailAttachmentException(self::ERROR_INVALID_FILE_NAME);
        }

        $this->fileName = $fileName;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * @throws \Acme\App\Core\Port\Notification\Client\Email\Exception\EmailAttachmentException
     */
    protected function setContentType(string $contentType): void
    {
        if (!\is_string($contentType) || empty($contentType)) {
            throw new EmailAttachmentException(self::ERROR_INVALID_CONTENT_TYPE);
        }

        $this->contentType = $contentType;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @throws EmailAttachmentException
     */
    protected function setContent(string $content): void
    {
        if (!is_string($content) || empty($content)) {
            throw new EmailAttachmentException(self::ERROR_INVALID_CONTENT);
        }
        $this->content = $content;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        // base 64 encode the binary content when serializing
        return serialize([
            $this->fileName,
            $this->contentType,
            base64_encode($this->content),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized): void
    {
        list($this->fileName, $this->contentType, $this->content) = unserialize($serialized);
        $this->content = base64_decode($this->content, true);
    }
}
