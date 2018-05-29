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
 * @author Ruud Van Der Weiijde
 * @author Herberto Graca <herberto.graca@gmail.com>
 */
interface EmailAttachmentInterface
{
    public function getFileName(): string;

    public function getContentType(): string;

    public function getContent(): string;
}
