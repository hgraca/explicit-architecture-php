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

namespace Acme\App\Infrastructure\TemplateEngine\Twig;

use Acme\App\Core\Port\TemplateEngine\NullTemplateViewModel;
use Acme\App\Core\Port\TemplateEngine\TemplateEngineInterface;
use Acme\App\Core\Port\TemplateEngine\TemplateViewModelInterface;
use Psr\Http\Message\ResponseInterface;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

final class TemplateEngine implements TemplateEngineInterface
{
    /**
     * @var EngineInterface
     */
    private $templateEngine;

    /**
     * @var HttpFoundationFactoryInterface
     */
    private $symfonyResponseFactory;

    /**
     * @var HttpMessageFactoryInterface
     */
    private $psrResponseFactory;

    public function __construct(
        EngineInterface $templateEngine,
        HttpFoundationFactoryInterface $symfonyResponseFactory,
        HttpMessageFactoryInterface $psrResponseFactory
    ) {
        $this->templateEngine = $templateEngine;
        $this->symfonyResponseFactory = $symfonyResponseFactory;
        $this->psrResponseFactory = $psrResponseFactory;
    }

    /**
     * @throws \ReflectionException
     */
    public function render(string $template, TemplateViewModelInterface $viewModel = null): string
    {
        return $this->templateEngine->render(
            $template,
            $this->extractParametersFromViewModel($viewModel ?? new NullTemplateViewModel())
        );
    }

    /**
     * @throws \ReflectionException
     */
    public function renderResponse(
        string $template,
        TemplateViewModelInterface $viewModel = null,
        ResponseInterface $response = null
    ): ResponseInterface {
        if ($response) {
            $response = $this->symfonyResponseFactory->createResponse($response);
        }

        $parameters = $this->extractParametersFromViewModel($viewModel ?? new NullTemplateViewModel());

        $response = $this->psrResponseFactory->createResponse(
            $this->templateEngine->renderResponse($template, $parameters, $response)
        );

        $response->getBody()->rewind();

        return $response;
    }

    public function exists(string $template): bool
    {
        return $this->templateEngine->exists($template);
    }

    /**
     * @throws \ReflectionException
     */
    private function extractParametersFromViewModel(TemplateViewModelInterface $viewModel): array
    {
        $parameters = ['viewModel' => $viewModel];
        $viewModelReflection = new ReflectionClass($viewModel);
        $methods = $viewModelReflection->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            if ($this->methodNameHasTemplatePrefix($method)) {
                $parameters[$this->generateTemplateVariableName($method)] = $method->invoke($viewModel);
            }
        }

        return $parameters;
    }

    private function methodNameHasTemplatePrefix(ReflectionMethod $method): bool
    {
        return (bool) preg_match('/^(' . self::PARSED_METHOD_PREFIXES . ')/', $method->getName());
    }

    private function generateTemplateVariableName(ReflectionMethod $method): string
    {
        return lcfirst(str_replace('get', '', $method->getName()));
    }
}
