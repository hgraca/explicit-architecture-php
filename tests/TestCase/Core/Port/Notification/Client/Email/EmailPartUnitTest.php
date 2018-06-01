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

use Acme\App\Core\Port\Notification\Client\Email\EmailPart;
use Acme\App\Test\Framework\AbstractUnitTest;

/**
 * @author Herberto Graca <herberto.graca@gmail.com>
 * @author Marijn Koesen
 */
final class EmailPartUnitTest extends AbstractUnitTest
{
    /**
     * @test
     * @dataProvider getMessageParts
     */
    public function getters_work_as_expected(?string $content, string $type = null, string $charset = null): void
    {
        $messagePart = new EmailPart($content, $type, $charset);
        $this->assertEquals($content, $messagePart->getContent());
        $this->assertEquals($type, $messagePart->getContentType());
        $this->assertEquals($charset, $messagePart->getCharset());
    }

    public function getMessageParts(): array
    {
        return [
            ['ct'],
            ['ct', 'tp'],
            ['ct', 'tp', 'ch'],
        ];
    }
}
