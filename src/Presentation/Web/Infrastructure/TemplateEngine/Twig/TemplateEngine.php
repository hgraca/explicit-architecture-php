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

namespace Acme\App\Presentation\Web\Infrastructure\TemplateEngine\Twig;

use Acme\App\Presentation\Web\Core\Port\TemplateEngine\TemplateEngineInterface;
use Psr\Http\Message\ResponseInterface;
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

    public function render(string $template, array $parameters = []): string
    {
        return $this->templateEngine->render($template, $parameters);
    }

    public function renderResponse(
        string $template,
        array $parameters = [],
        ResponseInterface $response = null
    ): ResponseInterface {
        if ($response) {
            $response = $this->symfonyResponseFactory->createResponse($response);
        }

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
}
