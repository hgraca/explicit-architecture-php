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

namespace Acme\App\Presentation\Web\Infrastructure\TemplateEngine\Twig\Extension\SourceCode;

use ReflectionFunction;
use ReflectionMethod;
use ReflectionObject;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Template;
use Twig\TwigFunction;
use Twig_Template;
use Twig_TemplateWrapper;

/**
 * CAUTION: this is an extremely advanced Twig extension. It's used to get the
 * source code of the controller and the template used to render the current
 * page. If you are starting with Symfony, don't look at this code and consider
 * studying instead the code of the Md2HtmlExtension.php extension.
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class SourceCodeExtension extends AbstractExtension
{
    /**
     * @var callable| null
     */
    private $controller;

    public function setController(?callable $controller): void
    {
        $this->controller = $controller;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('show_source_code', [$this, 'showSourceCode'], ['is_safe' => ['html'], 'needs_environment' => true]),
        ];
    }

    /**
     * @param string|Twig_Template|Twig_TemplateWrapper|array $template
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function showSourceCode(Environment $twig, $template): string
    {
        return $twig->render('@Web/Infrastructure/TemplateEngine/Twig/Extension/SourceCode/source_code.html.twig', [
            'controller' => $this->getController(),
            'template' => $this->getTemplateSource($twig->resolveTemplate($template)),
        ]);
    }

    private function getController(): ?array
    {
        // this happens for example for exceptions (404 errors, etc.)
        if ($this->controller === null) {
            return null;
        }

        $method = $this->getCallableReflector($this->controller);

        $classCode = file($method->getFileName());
        $methodCode = \array_slice($classCode, $method->getStartLine() - 1, $method->getEndLine() - $method->getStartLine() + 1);
        $controllerCode = '    ' . $method->getDocComment() . "\n" . implode('', $methodCode);

        return [
            'file_path' => $method->getFileName(),
            'starting_line' => $method->getStartLine(),
            'source_code' => $this->unIndentCode($controllerCode),
        ];
    }

    /**
     * Gets a reflector for a callable.
     *
     * This logic is copied from Symfony\Component\HttpKernel\Controller\ControllerResolver::getArguments
     *
     * @throws \ReflectionException
     */
    private function getCallableReflector(callable $callable): \ReflectionFunctionAbstract
    {
        if (\is_array($callable)) {
            return new ReflectionMethod($callable[0], $callable[1]);
        }

        if (\is_object($callable) && !$callable instanceof \Closure) {
            $r = new ReflectionObject($callable);

            return $r->getMethod('__invoke');
        }

        return new ReflectionFunction($callable);
    }

    private function getTemplateSource(Template $template): array
    {
        $templateSource = $template->getSourceContext();

        return [
            // Twig templates are not always stored in files (they can be stored
            // in a database for example). However, for the needs of the Symfony
            // Demo app, we consider that all templates are stored in files and
            // that their file paths can be obtained through the source context.
            'file_path' => $templateSource->getPath(),
            'starting_line' => 1,
            'source_code' => $templateSource->getCode(),
        ];
    }

    /**
     * Utility method that "unIndents" the given $code when all its lines start
     * with a tabulation of four white spaces.
     */
    private function unIndentCode(string $code): string
    {
        $formattedCode = $code;
        $codeLines = explode("\n", $code);

        $indentedLines = array_filter($codeLines, function ($lineOfCode) {
            return $lineOfCode === '' || mb_substr($lineOfCode, 0, 4) === '    ';
        });

        if (\count($indentedLines) === \count($codeLines)) {
            $formattedCode = array_map(function ($lineOfCode) {
                return mb_substr($lineOfCode, 4);
            }, $codeLines);
            $formattedCode = implode("\n", $formattedCode);
        }

        return $formattedCode;
    }
}
