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

use Acme\App\Core\Port\Notification\Client\Email\EmailAttachment;
use Acme\App\Core\Port\Notification\Client\Email\Exception\EmailAttachmentException;
use Acme\App\Test\Framework\AbstractUnitTest;

/**
 * @author Herberto Graca <herberto.graca@gmail.com>
 * @author Ruud Van Der Weijde
 *
 * @small
 *
 * @internal
 */
final class EmailAttachmentUnitTest extends AbstractUnitTest
{
    const VALID_CONTENT_AS_BASE64 = 'R0lGODlhEAAOALMAAOazToeHh0tLS/7LZv/0jvb29t/f3//Ub//ge8WSLf/rhf/3kdbW1mxsbP//mf///yH5BAAAAAAALAAAAAAQAA4AAARe8L1Ekyky67QZ1hLnjM5UUde0ECwLJoExKcppV0aCcGCmTIHEIUEqjgaORCMxIC6e0CcguWw6aFjsVMkkIr7g77ZKPJjPZqIyd7sJAgVGoEGv2xsBxqNgYPj/gAwXEQA7';
    const VALID_CONTENT_TYPE = 'image/gif';
    const VALID_FILE_NAME = 'file.gif';

    /**
     * @test
     */
    public function construct_with_valid_input_does_not_throw_exception(): void
    {
        $validContent = base64_decode(self::VALID_CONTENT_AS_BASE64, true);

        new EmailAttachment(self::VALID_FILE_NAME, self::VALID_CONTENT_TYPE, $validContent);

        self::assertTrue(true); // If it reaches here it means no exception was thrown, so the test passes
    }

    /**
     * @test
     * @dataProvider getInvalidInput
     */
    public function construct_with_invalid_input_throws_exception(
        string $fileName,
        string $contentType,
        string $content,
        string $expectedExceptionMessage
    ): void {
        $this->expectException(EmailAttachmentException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        new EmailAttachment($fileName, $contentType, $content);
    }

    public function getInvalidInput(): array
    {
        $validContent = base64_decode(self::VALID_CONTENT_AS_BASE64, true);

        return [
            'emptyFileName' => [
                'fileName' => '',
                'contentType' => self::VALID_CONTENT_TYPE,
                'content' => $validContent,
                'error' => EmailAttachment::ERROR_INVALID_FILE_NAME,
            ],
            'emptyContentType' => [
                'fileName' => self::VALID_FILE_NAME,
                'contentType' => '',
                'content' => $validContent,
                'error' => EmailAttachment::ERROR_INVALID_CONTENT_TYPE,
            ],
            'emptyContent' => [
                'fileName' => self::VALID_FILE_NAME,
                'contentType' => self::VALID_CONTENT_TYPE,
                'content' => '',
                'error' => EmailAttachment::ERROR_INVALID_CONTENT,
            ],
        ];
    }

    /**
     * @test
     */
    public function serialization_works_as_expected(): void
    {
        $validContent = base64_decode(self::VALID_CONTENT_AS_BASE64, true);
        $emailAttachment = new EmailAttachment(self::VALID_FILE_NAME, self::VALID_CONTENT_TYPE, $validContent);

        $expectedEmailAttachment = clone $emailAttachment;
        $emailAttachmentAfterSerialization = unserialize(serialize($emailAttachment));

        self::assertEquals($expectedEmailAttachment, $emailAttachmentAfterSerialization);
    }
}
