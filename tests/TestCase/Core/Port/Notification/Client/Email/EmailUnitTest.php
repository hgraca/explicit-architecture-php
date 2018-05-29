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

use Acme\App\Core\Port\Notification\Client\Email\Email;
use Acme\App\Core\Port\Notification\Client\Email\EmailAddress;
use Acme\App\Core\Port\Notification\Client\Email\MimeType;
use Acme\App\Test\Framework\AbstractUnitTest;
use Exception;

/**
 * @author Herberto Graca <herberto.graca@gmail.com>
 * @author Jeroen Van Den Heuvel
 * @author Marijn Koesen
 * @author Rodrigo Prestes
 * @author Ruud Van Der Weiijde
 */
class EmailUnitTest extends AbstractUnitTest
{
    /**
     * @test
     * @dataProvider getSubjectAndFromAddress
     */
    public function constructorPass(string $subject, EmailAddress $from): void
    {
        $message = new Email($subject, $from);
        $this->assertEquals($subject, $message->getSubject());
        $this->assertEquals($from, $message->getFrom());
    }

    public function getSubjectAndFromAddress(): array
    {
        return [
            ['', new EmailAddress('john@doe.tld')],
            ['subject', new EmailAddress('john@doe.tld', 'johnny doe')],
        ];
    }

    /**
     * @test
     * @dataProvider getHtmlContentAndCharset
     *
     * @throws Exception
     */
    public function setBodyHtmlPass(string $content, string $contentType, string $charset = null): void
    {
        $message = $this->getSimpleEmail();
        $message = $this->setContentAndCharsetBasedOnContentType($content, $contentType, $charset ?? '', $message);
        $this->assertBodyMatchesGivenContentAndContentType($message, $content, $contentType);
    }

    /**
     * @throws Exception
     */
    private function assertBodyMatchesGivenContentAndContentType(Email $message, string $content, string $contentType): void
    {
        $parts = $message->getParts();
        $this->assertTrue(\is_array($parts));
        foreach ($parts as $part) {
            if ($part->getContentType() === $contentType) {
                $this->assertEquals($content, $part->getContent());

                return;
            }
        }

        throw new Exception('Something went wrong...');
    }

    /**
     * @test
     * @dataProvider getHtmlContentAndCharset
     * @expectedException \Acme\App\Core\Port\Notification\Client\Email\Exception\EmailPartAlreadyProvidedException
     */
    public function setBodyHtmlTwiceFails(string $content, string $contentType, string $charset = null): void
    {
        $message = new Email('subbject', new EmailAddress('from@address.tld'));
        $message = $this->setContentAndCharsetBasedOnContentType($content, $contentType, $charset ?? '', $message);
        $this->setContentAndCharsetBasedOnContentType($content, $contentType, $charset ?? '', $message);
    }

    /**
     * @return array
     */
    public function getHtmlContentAndCharset(): array
    {
        $contentHtml = '<p>UTF-8 Characters: ö ü ä</p><p>UTF-8 Chinese: 激 光 這 </p><p>HTML Entity Characters: &#28450; &#23383;</p>';
        $contentText = "UTF-8 Characters: ö ü ä\n\nUTF-8 Chinese: 激 光 這 \n\nHTML Entity Characters: &#28450; &#23383;";
        $contentTypeHtml = MimeType::MIME_TYPE_HTML;
        $contentTypeText = MimeType::MIME_TYPE_PLAIN;

        return [
            [$contentHtml, $contentTypeHtml],
            [$contentText, $contentTypeText],
            [$contentHtml, $contentTypeHtml, 'utf-8'],
            [$contentText, $contentTypeText, 'utf-8'],
        ];
    }

    /**
     * @test
     */
    public function getFirstTo(): void
    {
        $firstEmail = 'first@example.org';
        $secondEmail = 'second@example.org';

        $message = new Email('subject', new EmailAddress('from@address.tld'));
        $message->addTo(new EmailAddress($firstEmail));
        $message->addTo(new EmailAddress($secondEmail));
        $this->assertEquals($firstEmail, $message->getFirstTo()->getEmail());
    }

    /**
     * @test
     */
    public function getFirstToMatching(): void
    {
        $firstEmail = 'first@example.org';
        $secondEmail = 'second@example.org';

        $message = new Email('subject', new EmailAddress('from@address.tld'));
        $message->addTo(new EmailAddress($firstEmail));
        $message->addTo(new EmailAddress($secondEmail));

        $this->assertEquals($secondEmail, $message->getFirstToMatchingRegex('/^second.*/')->getEmail());
    }

    /**
     * @test
     * @expectedException \Acme\App\Core\Port\Notification\Client\Email\Exception\EmailAddressNotFoundException
     * @expectedExceptionMessage No e-mail address found with the pattern /not-expected/
     */
    public function getFirstToMatchingNone(): void
    {
        $firstEmail = 'first@example.org';
        $secondEmail = 'second@example.org';

        $message = new Email('subject', new EmailAddress('from@address.tld'));
        $message->addTo(new EmailAddress($firstEmail));
        $message->addTo(new EmailAddress($secondEmail));

        $this->assertEquals($secondEmail, $message->getFirstToMatchingRegex('/not-expected/')->getEmail());
    }

    /**
     * @test
     */
    public function plainTextPart(): void
    {
        $plainText = 'Plain text part';
        $message = new Email('subject', new EmailAddress('from@address.tld'));
        $message->setBodyText($plainText);
        $this->assertEquals($plainText, $message->getPlainTextPart()->getContent());
    }

    /**
     * @test
     */
    public function htmlPart(): void
    {
        $html = '<html><body>Hello World</body></html>';
        $message = new Email('subject', new EmailAddress('from@address.tld'));
        $message->setBodyHtml($html);
        $this->assertEquals($html, $message->getHtmlPart()->getContent());
    }

    /**
     * @test
     */
    public function notContainingPartsYet(): void
    {
        $message = new Email('subject', new EmailAddress('from@address.tld'));
        $this->assertEmpty($message->getHtmlPart());
        $this->assertEmpty($message->getPlainTextPart());
    }

    /**
     * @test
     * @dataProvider getAddresses
     */
    public function messageTo(string $toAddress): void
    {
        $message = $this->getSimpleEmail();
        $message->addTo(new EmailAddress($toAddress));
        $mailAddresses = $message->getTo();
        $this->assertTrue(\is_array($mailAddresses));
        $this->assertEquals($toAddress, $mailAddresses[0]->getEmail());
    }

    /**
     * @test
     * @dataProvider getMultipleAddresses
     */
    public function messageMultipleTo(string $toAddressOne, string $toAddressTwo): void
    {
        $message = $this->getSimpleEmail();
        $message->addTo(new EmailAddress($toAddressOne));
        $message->addTo(new EmailAddress($toAddressTwo));
        $mailAddresses = $message->getTo();
        $this->assertTrue(\is_array($mailAddresses));
        $this->assertEquals($toAddressOne, $mailAddresses[0]->getEmail());
        $this->assertEquals($toAddressTwo, $mailAddresses[1]->getEmail());
    }

    /**
     * @test
     * @dataProvider getAddresses
     */
    public function messageCc(string $ccAddress): void
    {
        $message = $this->getSimpleEmail();
        $message->addCc(new EmailAddress($ccAddress));
        $mailAddresses = $message->getCc();
        $this->assertTrue(\is_array($mailAddresses));
        $this->assertEquals($ccAddress, $mailAddresses[0]->getEmail());
    }

    /**
     * @test
     * @dataProvider getMultipleAddresses
     */
    public function messageTwoCc(string $ccAddressOne, string $ccAddressTwo): void
    {
        $message = $this->getSimpleEmail();
        $message->addCc(new EmailAddress($ccAddressOne));
        $message->addCc(new EmailAddress($ccAddressTwo));
        $mailAddresses = $message->getCc();
        $this->assertTrue(\is_array($mailAddresses));
        $this->assertEquals($ccAddressOne, $mailAddresses[0]->getEmail());
        $this->assertEquals($ccAddressTwo, $mailAddresses[1]->getEmail());
    }

    /**
     * @test
     * @dataProvider getAddresses
     */
    public function messageBcc(string $bccAddress): void
    {
        $message = $this->getSimpleEmail();
        $message->addBcc(new EmailAddress($bccAddress));
        $mailAddresses = $message->getBcc();
        $this->assertTrue(\is_array($mailAddresses));
        $this->assertEquals($bccAddress, $mailAddresses[0]->getEmail());
    }

    /**
     * @test
     * @dataProvider getMultipleAddresses
     */
    public function messageTwoBcc(string $bccAddressOne, string $bccAddressTwo): void
    {
        $message = $this->getSimpleEmail();
        $message->addBcc(new EmailAddress($bccAddressOne));
        $message->addBcc(new EmailAddress($bccAddressTwo));
        $mailAddresses = $message->getBcc();
        $this->assertTrue(\is_array($mailAddresses));
        $this->assertEquals($bccAddressOne, $mailAddresses[0]->getEmail());
        $this->assertEquals($bccAddressTwo, $mailAddresses[1]->getEmail());
    }

    public function getAddresses(): array
    {
        return [
            ['uncle@bob.com'],
            ['a+b@c'],
            ['a'],
            [''],
        ];
    }

    public function getMultipleAddresses(): array
    {
        // according to RFC5321 http://tools.ietf.org/html/rfc5321
        $maxAllowedEmailAddressString = $this->getMaxAllowedMailAddressWithMaxLocalPart();
        $emailAddressTooLong = 'abc.' . $maxAllowedEmailAddressString . '.net';

        return [
            ['', ''],
            ['uncle@bob.com', ''],
            ['a@b.tld', '', 'a+b@c'],
            ['', 'a@b', 'z@z', $maxAllowedEmailAddressString],
            [$emailAddressTooLong, $emailAddressTooLong, $emailAddressTooLong, $emailAddressTooLong],
        ];
    }

    /**
     * @test
     * @dataProvider getHeaders
     *
     * @throws Exception
     */
    public function addHeaders($headerValuePairs): void
    {
        $expected = [];
        $message = $this->getSimpleEmail();
        foreach ($headerValuePairs as $headerValuePair) {
            $expected[$headerValuePair['key']] = $headerValuePair['value'];
            $message->addHeader($headerValuePair['key'], $headerValuePair['value']);
        }
        $this->assertEqualHeaders($message, $expected);
    }

    /**
     * @throws Exception
     */
    public function assertEqualHeaders(Email $message, array $expected): void
    {
        $headers = $message->getHeaders();
        foreach ($headers as $header) {
            $key = $header->getName();
            $value = $header->getValue();
            if (!array_key_exists($key, $expected)) {
                throw new Exception(
                    sprintf(
                        'Failed to set header [%s] with value [%s]',
                        var_export($key, true),
                        var_export($value, true)
                    )
                );
            }
            if ($expected[$key] === $value) {
                unset($expected[$key]);
            }
        }
        $this->assertEquals(0, \count($expected));
    }

    public function getHeaders(): array
    {
        return [
            [
                [['key' => '', 'value' => '']],
            ],
            [
                [['key' => 'keyOne', 'value' => '']],
            ],
            [
                [['key' => 'keyOne', 'value' => 'valueOne']],
            ],
            [
                [
                    ['key' => 'keyOne', 'value' => 'valueOne'],
                    ['key' => 'keyOne', 'value' => 'valueTwo'],
                    ['key' => 'keyOne', 'value' => 'valueThree'],
                ],
            ],
            [
                [
                    ['key' => 'keyOne', 'value' => 'valueOne'],
                    ['key' => 'keyTwo', 'value' => 'valueTwo'],
                    ['key' => 'keyThree', 'value' => 'valueThree'],
                ],
            ],
        ];
    }

    /**
     * @test
     * @dataProvider getTrackingCodeValues
     */
    public function setTrackMessageOpening(string $trackingCodeMessageOpen): void
    {
        $message = $this->getSimpleEmail();
        $expected = $trackingCodeMessageOpen;
        $message->setTrackMessageOpening((bool) $trackingCodeMessageOpen);
        $this->assertEquals($expected, $message->shouldTrackMessageOpening());
    }

    /**
     * @test
     * @dataProvider getTrackingCodeValues
     */
    public function setTrackClicks(bool $trackingCodeMessageClick): void
    {
        $message = $this->getSimpleEmail();
        $expected = $trackingCodeMessageClick;
        $message->setTrackClicks($trackingCodeMessageClick);
        $this->assertEquals($expected, $message->shouldTrackClicks());
    }

    public function getTrackingCodeValues(): array
    {
        return [
            [true],
            [false],
        ];
    }

    /**
     * @test
     * @dataProvider getTrackingCampaigns
     */
    public function setTrackingCampaign(string $trackingCampaign): void
    {
        $message = $this->getSimpleEmail();
        $message->setTrackingCampaign($trackingCampaign);
        $this->assertEquals($trackingCampaign, $message->getTrackingCampaign());
    }

    public function getTrackingCampaigns(): array
    {
        return [
            [''],
            ['a'],
            [str_pad('abc123DEF!', 256)],
        ];
    }

    /**
     * @test
     * @dataProvider getTags
     */
    public function setTags(array $tags): void
    {
        $message = $this->getSimpleEmail();
        $message->setTags($tags);
        $messageTags = $message->getTags();
        $this->assertEquals($tags, $messageTags);
    }

    public function getTags(): array
    {
        return [
            [['']],
            [['', '', '']],
            [['', '', 'tag2', 'tag-3']],
            [['TaG1', '', '', 'tag-3']],
            [['TaG1', '', 'tag2', '']],
        ];
    }

    /**
     * @test
     */
    public function message(): void
    {
        $subject = 'test-subject';
        $emailAddress = ['mail' => 'test@example.com', 'name' => 'test-name'];
        $headers = [['key' => 'name-1'], ['key' => 'name-2', 'value' => 'value-2']];

        $body = [
            MimeType::MIME_TYPE_PLAIN => ['content' => 'body text test'],
            MimeType::MIME_TYPE_HTML => ['content' => '<h1>body html</h1><p>test</p>', 'characterset' => 'utf-8'],
        ];

        $message = new Email($subject, new EmailAddress($emailAddress['mail'], $emailAddress['name']));
        $message->addTo(new EmailAddress($emailAddress['mail'], $emailAddress['name']));
        $message->addCc(new EmailAddress($emailAddress['mail'], $emailAddress['name']));
        $message->addBcc(new EmailAddress($emailAddress['mail'], $emailAddress['name']));

        $message->setBodyText($body[MimeType::MIME_TYPE_PLAIN]['content']);
        $message->setBodyHtml(
            $body[MimeType::MIME_TYPE_HTML]['content'],
            $body[MimeType::MIME_TYPE_HTML]['characterset']
        );

        foreach ($headers as $header) {
            $value = isset($header['value']) ? $header['value'] : '';
            $message->addHeader($header['key'], $value);
        }

        $this->assertEquals($subject, $message->getSubject());

        foreach ($message->getTo() as $to) {
            $this->compareMailAddressWithArray($emailAddress, $to);
        }

        foreach ($message->getCc() as $cc) {
            $this->compareMailAddressWithArray($emailAddress, $cc);
        }

        foreach ($message->getBcc() as $bcc) {
            $this->compareMailAddressWithArray($emailAddress, $bcc);
        }

        $this->assertEquals(2, count($message->getParts()));

        foreach ($message->getParts() as $part) {
            $this->assertArrayHasKey($part->getContentType(), $body);
            $this->assertEquals($body[$part->getContentType()]['content'], $part->getContent());

            if (isset($body[$part->getContentType()]['characterset'])) {
                $this->assertEquals($body[$part->getContentType()]['characterset'], $part->getCharset());
            } else {
                $this->assertNull($part->getCharset());
            }
        }

        foreach ($message->getHeaders() as $messageHeader) {
            $headerMatches = false;
            foreach ($headers as $header) {
                if ($header['key'] === $messageHeader->getName()) {
                    $headerMatches = true;
                    if (isset($header['value'])) {
                        $this->assertEquals($header['value'], $messageHeader->getValue());
                    } else {
                        $this->assertEmpty($messageHeader->getValue());
                    }
                }
            }

            $this->assertTrue($headerMatches);
        }
    }

    private function compareMailAddressWithArray(array $expected, EmailAddress $given): void
    {
        $this->assertEquals($expected['mail'], $given->getEmail());
        $this->assertEquals($expected['name'], $given->getName());
    }

    private function setContentAndCharsetBasedOnContentType(string $content, string $contentType, string $charset, Email $message): Email
    {
        if ($contentType === MimeType::MIME_TYPE_HTML) {
            $message->setBodyHtml($content, $charset);
        }
        if ($contentType === MimeType::MIME_TYPE_PLAIN) {
            $message->setBodyText($content, $charset);
        }

        return $message;
    }

    private function getSimpleEmail(): Email
    {
        $message = new Email('subbject', new EmailAddress('from@address.tld'));

        return $message;
    }

    private function getMaxAllowedMailAddressWithMaxLocalPart(): string
    {
        return str_pad('abc', 64) . '@' . str_pad('abc', 185) . '.com';
    }
}
