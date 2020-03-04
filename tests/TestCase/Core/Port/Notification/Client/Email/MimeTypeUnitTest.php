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

namespace Acme\App\Test\TestCase\Core\Port\Notification\Client\Email;

use Acme\App\Core\Port\Notification\Client\Email\MimeType;
use Acme\App\Test\Framework\AbstractUnitTest;

/**
 * @author Herberto Graca <herberto.graca@gmail.com>
 * @author Marijn Koesen
 *
 * @small
 *
 * @internal
 */
final class MimeTypeUnitTest extends AbstractUnitTest
{
    /**
     * @test
     */
    public function mime_type_text(): void
    {
        $txt = MimeType::MIME_TYPE_PLAIN;
        self::assertEquals($txt, 'text/plain');
    }

    /**
     * @test
     */
    public function mime_type_html(): void
    {
        $txt = MimeType::MIME_TYPE_HTML;
        self::assertEquals($txt, 'text/html');
    }
}
