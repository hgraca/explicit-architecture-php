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

namespace Acme\App\Test\Framework;

use Acme\App\Test\Framework\Container\ContainerAwareTestTrait;
use Acme\App\Test\Framework\Database\DatabaseAwareTestTrait;
use Acme\App\Test\Framework\Mock\MockTrait;
use Acme\PhpExtension\Helper\StringHelper;
use Hgraca\DoctrineTestDbRegenerationBundle\EventSubscriber\DatabaseAwareTestInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\SwiftmailerBundle\DataCollector\MessageDataCollector;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A functional test will test the application as a black box, emulating user http requests.
 * Usually, for this, we need to get services out of the container, etc. This top class makes it easier.
 * Furthermore, as the integration tests, the functional tests need to boot the application and have the overhead of
 * simulating http requests so they are even slower than the Integration tests.
 */
abstract class AbstractFunctionalTest extends WebTestCase implements DatabaseAwareTestInterface, AppTestInterface
{
    use AppTestTrait;
    use ContainerAwareTestTrait;
    use DatabaseAwareTestTrait;
    use MockeryPHPUnitIntegration;
    use MockTrait;

    /**
     * @var Client
     */
    private $client;

    protected function getClient(array $options = [], array $server = []): Client
    {
        return $this->client ?? $this->client = parent::createClient($options, $server);
    }

    protected function getContainer(): ContainerInterface
    {
        return $this->getClient()->getContainer();
    }

    protected static function assertResponseStatusCode(
        int $expectedResponseCode,
        Client $client,
        string $message = ''
    ): void {
        $response = $client->getResponse();
        $message = $message ? $message . "\n" : '';

        self::assertSame(
            $expectedResponseCode,
            $actualStatusCode = $response->getStatusCode(),
            $message . "The response status code doesn't match the expected value. \n"
                . "Expected $expectedResponseCode and got $actualStatusCode. \n"
                . "The response title was: '" . trim($client->getCrawler()->filterXPath('//title')->text()) . "'\n"
//                . "The response content was: '" . trim($client->getResponse()->getContent()) . "'"
        );
    }

    /**
     * This assertion requires that the FunctionalTest enables the profiler before making the http request with:
     *      `$client->enableProfiler();`
     * And that the request does not follow redirects automatically.
     *
     * @see http://symfony.com/doc/current/email/testing.html
     */
    protected static function assertEmailWasSent(
        MessageDataCollector $mailCollector,
        string $fromEmail = null,
        string $toEmail = null,
        string $subject = null,
        string ...$bodyPartList
    ): void {
        /** @var Swift_Message[] $collectedEmailList */
        $collectedEmailList = $mailCollector->getMessages();

        self::assertGreaterThan(0, \count($collectedEmailList), 'No email has been sent.');

        foreach ($collectedEmailList as $message) {
            if (
                $message instanceof Swift_Message
                && (!$fromEmail || $fromEmail === key($message->getFrom()))
                && (!$toEmail || $toEmail === key($message->getTo()))
                && (!$subject || $subject === $message->getSubject())
                && self::emailBodyContainsAllParts($message, ...$bodyPartList)
            ) {
                self::assertTrue(true);

                return;
            }
        }
        self::fail(
            "Emails were sent, but could not find an email from '$fromEmail', to '$toEmail', with subject '$subject'"
            . " containing '" . implode("', '", $bodyPartList) . "'.\n"
            . "Emails sent: \n"
            . self::formatSentEmailList($collectedEmailList)
        );
    }

    private static function emailBodyContainsAllParts(Swift_Message $message, string ...$bodyPartList): bool
    {
        foreach ($bodyPartList as $bodyPart) {
            if (!StringHelper::contains($bodyPart, $message->getBody())) {
                return false;
            }
        }

        return true;
    }

    private static function formatSentEmailList($collectedEmailList): string
    {
        $messageList = '';
        foreach ($collectedEmailList as $message) {
            if ($message instanceof Swift_Message) {
                $fromEmail = key($message->getFrom());
                $toEmail = key($message->getTo());
                $subject = $message->getSubject();
                $messageList .= "From: $fromEmail, To: $toEmail, Subject: $subject\n";
            }
        }

        return $messageList;
    }
}
