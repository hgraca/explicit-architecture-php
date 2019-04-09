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

use Acme\App\Core\Port\Notification\Client\Email\Email;
use Acme\App\Core\Port\Notification\Client\Email\EmailAddress;
use Acme\App\Core\Port\Router\UrlType;
use Acme\App\Test\Framework\Container\ContainerAwareTestTrait;
use Acme\App\Test\Framework\Database\DatabaseAwareTestTrait;
use Acme\App\Test\Framework\Decorator\EmailCollectorEmailerDecorator;
use Acme\App\Test\Framework\Mock\MockTrait;
use Acme\PhpExtension\Helper\StringHelper;
use Hgraca\DoctrineTestDbRegenerationBundle\EventSubscriber\DatabaseAwareTestInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

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
    use RoutingAwareTestTrait;

    /**
     * @var Client
     */
    private $client;

    protected function getHttpClient(array $options = [], array $server = []): Client
    {
        return $this->client ?? $this->client = self::createClient($options, $server);
    }

    protected function requestRoute(
        string $method,
        string $route,
        array $arguments = [],
        array $parameters = [],
        UrlType $type = null,
        array $server = [],
        string $content = null
    ): Response {
        $this->getHttpClient()->request(
            $method,
            $this->generateUrl($route, $arguments, $type),
            $parameters,
            $files = [],
            $server,
            $content
        );

        return $this->getHttpClient()->getResponse();
    }

    protected function getContainer(): ContainerInterface
    {
        return $this->getHttpClient()->getContainer();
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
     * This assertion requires that the FunctionalTest does not follow redirects automatically.
     *
     * We can not use an email collector like here http://symfony.com/doc/current/email/testing.html
     * because our emails are sent after the request is handled and the response is sent back.
     */
    protected function assertEmailWasSent(
        string $fromEmail = null,
        string $toEmail = null,
        string $subject = null,
        string ...$bodyPartList
    ): void {
        $collectedEmailList = $this->getSentEmails();

        self::assertGreaterThan(0, \count($collectedEmailList), 'No email has been sent.');

        foreach ($collectedEmailList as $email) {
            if (
                (!$fromEmail || $fromEmail === $email->getFrom()->getEmail())
                && (!$toEmail
                    || \in_array(
                        $toEmail,
                        array_map(
                            function (EmailAddress $emailAddress) {
                                return $emailAddress->getEmail();
                            },
                            $email->getTo()
                        ), true
                    )
                )
                && (!$subject || $subject === $email->getSubject())
                && self::emailBodyContainsAllParts($email, ...$bodyPartList)
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

    private static function emailBodyContainsAllParts(Email $email, string ...$bodyPartList): bool
    {
        foreach ($bodyPartList as $bodyPart) {
            if (
            !StringHelper::contains($bodyPart, $email->getHtmlPart()->getContent())
            && !StringHelper::contains($bodyPart, $email->getPlainTextPart()->getContent())
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param Email[] $collectedEmailList
     */
    private static function formatSentEmailList(array $collectedEmailList): string
    {
        $messageList = '';
        foreach ($collectedEmailList as $email) {
            $fromEmail = key($email->getFrom());
            $toEmail = key($email->getTo());
            $subject = $email->getSubject();
            $messageList .= "From: $fromEmail, To: $toEmail, Subject: $subject\n";
        }

        return $messageList;
    }

    /**
     * @return Email[]
     */
    private function getSentEmails(): array
    {
        return $this->getEmailer()->getSentEmails();
    }

    private function getEmailer(): EmailCollectorEmailerDecorator
    {
        return $this->getService(EmailCollectorEmailerDecorator::class);
    }
}
