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
use Hgraca\DoctrineTestDbRegenerationBundle\EventSubscriber\DatabaseAwareTestInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A functional test will test the application as a black box, emulating user http requests.
 * Usually, for this, we need to get services out of the container, etc. This top class makes it easier.
 * Furthermore, as the integration tests, the functional tests need to boot the application and have the overhead of
 * simulating http requests so they are even slower than the Integration tests.
 */
abstract class AbstractFunctionalTest extends WebTestCase implements DatabaseAwareTestInterface
{
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

    protected static function assertResponseStatusCode(int $expectedResponseCode, Client $client): void
    {
        $response = $client->getResponse();

        self::assertSame(
            $expectedResponseCode,
            $actualStatusCode = $response->getStatusCode(),
            "The response status code doesn't match the expected value. \n"
            . "Expected $expectedResponseCode and got $actualStatusCode. \n"
            . "The response title was: '" . trim($client->getCrawler()->filterXPath('//title')->text()) . "'"
        );
    }
}
