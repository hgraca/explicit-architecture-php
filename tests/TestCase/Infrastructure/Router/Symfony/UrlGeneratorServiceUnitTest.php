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

namespace Acme\App\Test\TestCase\Infrastructure\Router\Symfony;

use Acme\App\Core\Port\Router\UrlType;
use Acme\App\Infrastructure\Router\Symfony\UrlGeneratorService;
use Acme\App\Test\Framework\AbstractUnitTest;
use Mockery;
use Mockery\MockInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as SymfonyUrlGeneratorInterface;

/**
 * @small
 */
final class UrlGeneratorServiceUnitTest extends AbstractUnitTest
{
    /**
     * @var MockInterface|SymfonyUrlGeneratorInterface
     */
    private $symfonyUrlGeneratorMock;

    /**
     * @var UrlGeneratorService
     */
    private $urlGenerator;

    protected function setUp(): void
    {
        $this->symfonyUrlGeneratorMock = Mockery::mock(SymfonyUrlGeneratorInterface::class);
        $this->urlGenerator = new UrlGeneratorService($this->symfonyUrlGeneratorMock);
    }

    /**
     * @test
     * @dataProvider provideUrlType
     */
    public function generateUrl(?UrlType $urlType): void
    {
        $route = 'some_route';
        $parameters = ['a' => 'parameter'];

        $this->symfonyUrlGeneratorMock->shouldReceive('generate')
            ->once()
            ->with($route, $parameters, UrlType::absolutePath()->getValue())
            ->andReturn('some/u/r/l');

        $this->urlGenerator->generateUrl($route, $parameters, $urlType);
    }

    public function provideUrlType(): array
    {
        return [
            [UrlType::absolutePath()],
            [null],
        ];
    }
}
