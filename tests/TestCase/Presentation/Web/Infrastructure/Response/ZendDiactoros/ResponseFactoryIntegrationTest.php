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

namespace Acme\App\Test\TestCase\Presentation\Web\Infrastructure\Response\ZendDiactoros;

use Acme\App\Core\Port\Router\UrlGeneratorInterface;
use Acme\App\Presentation\Web\Core\Port\Response\ResponseFactoryInterface;
use Acme\App\Presentation\Web\Infrastructure\Response\ZendDiactoros\ResponseFactory;
use Acme\App\Test\Framework\AbstractIntegrationTest;
use stdClass;

final class ResponseFactoryIntegrationTest extends AbstractIntegrationTest
{
    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    protected function setUp(): void
    {
        $this->responseFactory = self::getService(ResponseFactoryInterface::class);
    }

    /**
     * @test
     * @dataProvider provideResponseData
     */
    public function respond(string $content, int $status, array $headers): void
    {
        $response = $this->responseFactory->respond($content, $status, $headers);

        self::assertSame($content, $response->getBody()->getContents());
        self::assertSame($status, $response->getStatusCode());

        foreach ($headers as $key => $value) {
            if (!\is_array($value)) {
                $headers[$key] = [$value];
            }
        }
        self::assertArraySubset($headers, $response->getHeaders());
    }

    public function provideResponseData(): array
    {
        return [
            ['', 200, []],
            ['some content', 200, ['a' => 'b', 'c' => ['d', 'e']]],
            ['some content', 200, ['a' => ['b'], 'c' => ['d', 'e']]],
            ['some content', 599, ['a' => ['b'], 'c' => ['d', 'e']]],
        ];
    }

    /**
     * @test
     * @dataProvider provideJsonResponseData
     */
    public function respondJson($content, int $status, array $headers): void
    {
        $response = $this->responseFactory->respondJson($content, $status, $headers);

        if (!\is_string($content)) {
            $content = \json_encode($content);
        }

        self::assertSame($content, $response->getBody()->getContents());
        self::assertSame($status, $response->getStatusCode());

        foreach ($headers as $key => $value) {
            if (!\is_array($value)) {
                $headers[$key] = [$value];
            }
        }
        self::assertArraySubset($headers, $response->getHeaders());
    }

    public function provideJsonResponseData(): array
    {
        $object = new stdClass();
        $object->a = 'some content';

        return [
            [[], 200, []],
            [[], 200, ['a' => 'b', 'c' => ['d', 'e']]],
            [[], 200, ['a' => ['b'], 'c' => ['d', 'e']]],
            [[], 599, ['a' => ['b'], 'c' => ['d', 'e']]],
            [['a' => 'some content'], 599, ['a' => ['b'], 'c' => ['d', 'e']]],
            [$object, 599, ['a' => ['b'], 'c' => ['d', 'e']]],
        ];
    }

    /**
     * @test
     */
    public function redirectToUrl(): void
    {
        $url = 'some/url';
        $status = 301;

        $response = $this->responseFactory->redirectToUrl($url, $status);

        self::assertSame($url, $response->getHeader('location')[0]);
        self::assertSame($status, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function redirectToRoute(): void
    {
        $route = 'homepage';
        $status = 301;

        $response = $this->responseFactory->redirectToRoute($route, [], $status);

        self::assertSame($this->getRouteUrl($route), $response->getHeader('location')[0]);
        self::assertSame($status, $response->getStatusCode());
    }

    private function getRouteUrl(string $route): string
    {
        return self::getService(UrlGeneratorInterface::class)->generateUrl($route);
    }
}
