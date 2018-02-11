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
use Acme\App\Presentation\Web\Infrastructure\TemplateEngine\Twig\TemplateEngine;
use Acme\App\Test\Framework\AbstractIntegrationTest;
use Zend\Diactoros\Response;

final class TemplateEngineIntegrationTest extends AbstractIntegrationTest
{
    private const TEMPLATE = '@Test/Infrastructure/TemplateEngine/Twig/test.html.twig';

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
     * @dataProvider provideTemplates
     */
    public function exists(string $template, bool $expectedResult): void
    {
        self::assertSame($expectedResult, $this->templateEngine->exists($template));
    }

    public function provideTemplates(): array
    {
        return [
            [self::TEMPLATE, true],
            ['@Test/unexisting_test.html.twig', false],
        ];
    }

    /**
     * @test
     */
    public function render(): void
    {
        $parameters = ['var1' => 'a', 'var2' => 'b'];
        $expectedHtml = 'a test template with a b';

        $resultHtml = $this->templateEngine->render(self::TEMPLATE, $parameters);

        self::assertSame($expectedHtml, trim($resultHtml));
    }

    /**
     * @test
     */
    public function renderResponse_without_a_base_response(): void
    {
        $status = 599;
        $originalResponse = (new Response())
            ->withStatus($status)
            ->withHeader('a', 'b')
            ->withHeader('c', ['d', 'e']);

        $parameters = ['var1' => 'a', 'var2' => 'b'];
        $expectedHtml = 'a test template with a b';

        $resultResponse = $this->templateEngine->renderResponse(self::TEMPLATE, $parameters, $originalResponse);

        self::assertSame($status, $resultResponse->getStatusCode());
        self::assertSame(['b'], $resultResponse->getHeader('a'));
        self::assertSame(['d', 'e'], $resultResponse->getHeader('c'));
        self::assertSame($expectedHtml, trim($resultResponse->getBody()->getContents()));
    }

    /**
     * @test
     */
    public function renderResponse_with_a_base_response(): void
    {
        $parameters = ['var1' => 'a', 'var2' => 'b'];
        $expectedHtml = 'a test template with a b';

        $resultHtml = $this->templateEngine->renderResponse(self::TEMPLATE, $parameters)->getBody()->getContents();

        self::assertSame($expectedHtml, trim($resultHtml));
    }
}
