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

namespace Acme\App\Presentation\Web\Core\Component;

use Acme\App\Presentation\Web\Core\Port\Response\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @see https://symfony.com/doc/current/routing/redirect_trailing_slash.html
 */
final class BackslashRedirectController extends AbstractController
{
    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public function removeTrailingSlash(ServerRequestInterface $request): ResponseInterface
    {
        $url = $request->getUri();
        $pathInfo = $url->getPath();

        $url = str_replace($pathInfo, rtrim($pathInfo, ' /'), (string) $url);

        return $this->responseFactory->redirectToUrl($url, 301);
    }
}
