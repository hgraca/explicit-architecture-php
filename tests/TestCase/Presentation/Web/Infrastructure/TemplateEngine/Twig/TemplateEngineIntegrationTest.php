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

namespace Acme\App\Test\TestCase\Presentation\Web\Infrastructure\TemplateEngine\Twig;

use Acme\App\Presentation\Web\Core\Port\TemplateEngine\TemplateEngineInterface;
use Acme\App\Presentation\Web\Core\Port\TemplateEngine\TemplateViewModelInterface;
use Acme\App\Presentation\Web\Infrastructure\TemplateEngine\Twig\TemplateEngine;
use Acme\App\Test\Framework\AbstractIntegrationTest;
use DateTime;
use ReflectionException;
use Zend\Diactoros\Response;

final class TemplateEngineIntegrationTest extends AbstractIntegrationTest
{
    private const TEMPLATE_1 = '@Test/Infrastructure/TemplateEngine/Twig/test1.html.twig';
    private const TEMPLATE_2 = '@Test/Infrastructure/TemplateEngine/Twig/test2.html.twig';

    /**
     * @var TemplateEngine
     */
    private $templateEngine;

    protected function setUp(): void
    {
        $this->templateEngine = self::getService(TemplateEngineInterface::class);
    }

    /**
     * @test
     *
     * @dataProvider provideTemplates
     */
    public function exists(string $template, bool $expectedResult): void
    {
        self::assertSame($expectedResult, $this->templateEngine->exists($template));
    }

    public function provideTemplates(): array
    {
        return [
            [self::TEMPLATE_1, true],
            ['@Test/unexisting_test.html.twig', false],
        ];
    }

    /**
     * @test
     * @dataProvider provideViewModelAndExpectedResult
     *
     * @throws ReflectionException
     */
    public function render(string $template, TemplateViewModelInterface $viewModel, string $expectedHtml): void
    {
        self::assertSame(
            $expectedHtml,
            trim($this->templateEngine->render($template, $viewModel))
        );
    }

    public function provideViewModelAndExpectedResult(): array
    {
        return [
            [self::TEMPLATE_1, new Test1TemplateViewModel('a', 'b'), 'a test template with a b'],
            [
                self::TEMPLATE_2,
                new Test2TemplateViewModel([0, 1], 'string', 42, new DateTime('2018-04-13')),
                'a test template with -0--1- string 42 April 13, 2018 00:00',
            ],
        ];
    }

    /**
     * @test
     *
     * @throws ReflectionException
     */
    public function renderResponse_with_a_base_response(): void
    {
        $status = 599;
        $originalResponse = (new Response())
            ->withStatus($status)
            ->withHeader('a', 'b')
            ->withHeader('c', ['d', 'e']);

        $resultResponse = $this->templateEngine->renderResponse(
            self::TEMPLATE_1,
            new Test1TemplateViewModel('a', 'b'),
            $originalResponse
        );

        self::assertSame($status, $resultResponse->getStatusCode());
        self::assertSame(['b'], $resultResponse->getHeader('a'));
        self::assertSame(['d', 'e'], $resultResponse->getHeader('c'));
        self::assertSame('a test template with a b', trim($resultResponse->getBody()->getContents()));
    }

    /**
     * @test
     *
     * @throws ReflectionException
     */
    public function renderResponse_without_a_base_response(): void
    {
        $resultHtml = $this->templateEngine->renderResponse(self::TEMPLATE_1, new Test1TemplateViewModel('a', 'b'))
            ->getBody()
            ->getContents();

        self::assertSame('a test template with a b', trim($resultHtml));
    }
}
