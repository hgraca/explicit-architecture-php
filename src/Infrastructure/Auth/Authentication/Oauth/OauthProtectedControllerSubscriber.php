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

namespace Acme\App\Infrastructure\Auth\Authentication\Oauth;

use Acme\App\Core\Port\Auth\Authentication\Oauth\OauthProtectedControllerInterface;
use Exception;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use function is_array;

final class OauthProtectedControllerSubscriber implements EventSubscriberInterface
{
    private const DEFAULT_PRIORITY = 20;

    /**
     * @var ResourceServer
     */
    private $resourceServer;

    /**
     * @var HttpMessageFactoryInterface
     */
    private $psrHttpMessageFactory;

    public function __construct(ResourceServer $resourceServer, HttpMessageFactoryInterface $psrHttpMessageFactory)
    {
        $this->resourceServer = $resourceServer;
        $this->psrHttpMessageFactory = $psrHttpMessageFactory;
    }

    /**
     * Return the subscribed events, their methods and possibly their priorities
     * (the higher the priority the earlier the method is called).
     *
     * @see http://symfony.com/doc/current/event_dispatcher.html#creating-an-event-subscriber
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => ['onKernelController', self::DEFAULT_PRIORITY],
            KernelEvents::EXCEPTION => ['onKernelException', self::DEFAULT_PRIORITY],
        ];
    }

    /**
     * @throws OAuthServerException
     */
    public function onKernelController(FilterControllerEvent $event): void
    {
        $controller = $event->getController();
        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format
         */
        if (!is_array($controller)) {
            return;
        }
        if ($controller[0] instanceof OauthProtectedControllerInterface) {
            $request = $event->getRequest();
            $psrRequest = $this->psrHttpMessageFactory->createRequest($request);
            try {
                $psrRequest = $this->resourceServer->validateAuthenticatedRequest($psrRequest);
            } catch (OAuthServerException $exception) {
                throw $exception;
            } catch (Exception $exception) {
                throw new OAuthServerException(
                    $exception->getMessage(),
                    0,
                    'unknown_error',
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }
            $this->enrichSymfonyRequestWithAuthData($request, $psrRequest);
        }
    }

    private function enrichSymfonyRequestWithAuthData(Request $request, ServerRequestInterface $psrRequest): void
    {
        $request = $request->request;
        $requestArray = $request->all();
        $requestArray['oauth_user_id'] = $psrRequest->getAttribute('oauth_user_id');
        $requestArray['oauth_access_token_id'] = $psrRequest->getAttribute('oauth_access_token_id');
        $requestArray['oauth_client_id'] = $psrRequest->getAttribute('oauth_client_id');
        $request->replace($requestArray);
    }

    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        $exception = $event->getException();
        if (!$exception instanceof OAuthServerException) {
            return;
        }
        $response = new JsonResponse(['error' => $exception->getMessage()], $exception->getHttpStatusCode());
        $event->setResponse($response);
    }
}
