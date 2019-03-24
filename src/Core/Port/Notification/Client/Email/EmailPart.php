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

/**
 * @author Marijn Koesen
 * @author Herberto Graca <herberto.graca@gmail.com>
 */
class EmailPart
{
    /**
     * @var string
     */
    private $content;

    /**
     * @var string|null
     */
    private $contentType;

    /**
     * @var string|null
     */
    private $charset;

    public function __construct(string $content, string $contentType = null, string $charset = null)
    {
        $this->content = $content;
        $this->contentType = $contentType;
        $this->charset = $charset;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    public function getCharset(): ?string
    {
        return $this->charset;
    }
}
