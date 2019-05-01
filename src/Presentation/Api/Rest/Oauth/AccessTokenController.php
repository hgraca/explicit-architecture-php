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

namespace Acme\App\Presentation\Api\Rest\Oauth;

use DateInterval;
use Exception;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\PasswordGrant;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class AccessTokenController
{
    /**
     * @var AuthorizationServer
     */
    private $authorizationServer;

    /**
     * @var PasswordGrant
     */
    private $passwordGrant;

    /**
     * @var HttpMessageFactoryInterface
     */
    private $psrHttpMessageFactory;

    public function __construct(
        AuthorizationServer $authorizationServer,
        PasswordGrant $passwordGrant,
        HttpMessageFactoryInterface $psrHttpMessageFactory
    ) {
        $this->authorizationServer = $authorizationServer;
        $this->passwordGrant = $passwordGrant;
        $this->psrHttpMessageFactory = $psrHttpMessageFactory;
    }

    /**
     * @throws Exception
     */
    public function post(ServerRequestInterface $request): ?ResponseInterface
    {
        try {
            $this->passwordGrant->setRefreshTokenTTL(new DateInterval('P1M'));
            $this->authorizationServer->enableGrantType($this->passwordGrant, new DateInterval('PT1H'));

            return $this->authorizationServer->respondToAccessTokenRequest($request, $this->createPsrResponse());
        } catch (OAuthServerException $e) {
            return $e->generateHttpResponse($this->createPsrResponse());
        } catch (Throwable $e) {
            return $this->createPsrResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function createPsrResponse($content = '', int $status = 200, array $headers = []): ResponseInterface
    {
        return $this->psrHttpMessageFactory->createResponse(new Response($content, $status, $headers));
    }
}
