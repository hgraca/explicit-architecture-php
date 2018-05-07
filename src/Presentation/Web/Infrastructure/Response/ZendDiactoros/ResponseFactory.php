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

namespace Acme\App\Presentation\Web\Infrastructure\Response\ZendDiactoros;

use Acme\App\Core\Port\Router\UrlGeneratorInterface;
use Acme\App\Presentation\Web\Core\Port\Response\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class ResponseFactory implements ResponseFactoryInterface
{
    /**
     * @var HttpKernelInterface
     */
    private $httpKernel;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var HttpFoundationFactoryInterface
     */
    private $symfonyResponseFactory;

    /**
     * @var HttpMessageFactoryInterface
     */
    private $psrResponseFactory;

    public function __construct(
        HttpKernelInterface $httpKernel,
        UrlGeneratorInterface $urlGenerator,
        HttpFoundationFactoryInterface $symfonyResponseFactory,
        HttpMessageFactoryInterface $psrResponseFactory
    ) {
        $this->httpKernel = $httpKernel;
        $this->urlGenerator = $urlGenerator;
        $this->symfonyResponseFactory = $symfonyResponseFactory;
        $this->psrResponseFactory = $psrResponseFactory;
    }

    public function respond($content = '', int $status = 200, array $headers = []): ResponseInterface
    {
        $response = $this->psrResponseFactory->createResponse(new Response($content, $status, $headers));

        $response->getBody()->rewind();

        return $response;
    }

    public function respondJson($data = null, int $status = 200, array $headers = []): ResponseInterface
    {
        $response = $this->psrResponseFactory->createResponse(new JsonResponse($data, $status, $headers));

        $response->getBody()->rewind();

        return $response;
    }

    /**
     * @throws \Exception
     */
    public function forward(
        ServerRequestInterface $currentRequest,
        string $controller,
        array $attributes = null,
        array $queryParameters = null,
        array $postParameters = null
    ): ResponseInterface {
        $symfonyRequest = $this->symfonyResponseFactory->createRequest($currentRequest);

        $attributes['_forwarded'] = $symfonyRequest->attributes;
        $attributes['_controller'] = $controller;
        $subRequest = $symfonyRequest->duplicate($queryParameters, $postParameters, $attributes);

        $response = $this->httpKernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);

        if ($response instanceof ResponseInterface) {
            $response->getBody()->rewind();

            return $response;
        }

        $response = $this->psrResponseFactory->createResponse($response);

        $response->getBody()->rewind();

        return $response;
    }

    public function redirectToUrl(string $url, int $status = 302): ResponseInterface
    {
        return $this->psrResponseFactory->createResponse(new RedirectResponse($url, $status));
    }

    public function redirectToRoute(string $route, array $parameters = [], int $status = 302): ResponseInterface
    {
        return $this->psrResponseFactory->createResponse(
            new RedirectResponse($this->urlGenerator->generateUrl($route, $parameters), $status)
        );
    }
}
