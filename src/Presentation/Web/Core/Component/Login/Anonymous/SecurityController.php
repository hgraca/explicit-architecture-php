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

namespace Acme\App\Presentation\Web\Core\Component\Login\Anonymous;

use Acme\App\Core\Port\Auth\AuthenticationServiceInterface;
use Acme\App\Core\Port\TemplateEngine\TemplateEngineInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Controller used to manage the application security.
 * See https://symfony.com/doc/current/cookbook/security/form_login_setup.html.
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 * @author Herberto Graca <herberto.graca@gmail.com>
 */
class SecurityController
{
    /**
     * @var TemplateEngineInterface
     */
    private $templateEngine;

    /**
     * @var AuthenticationServiceInterface
     */
    private $authenticationService;

    public function __construct(
        TemplateEngineInterface $templateEngine,
        AuthenticationServiceInterface $authenticationService
    ) {
        $this->templateEngine = $templateEngine;
        $this->authenticationService = $authenticationService;
    }

    public function login(ServerRequestInterface $request): ResponseInterface
    {
        return $this->templateEngine->renderResponse(
            '@Login/Anonymous/login.html.twig',
            LoginViewModel::fromLastUsernameAndError(
                $this->authenticationService->getLastAuthenticationUsername($request),
                $this->authenticationService->getLastAuthenticationError($request)
            )
        );
    }

    /**
     * This is the route the user can use to logout.
     *
     * But, this will never be executed. Symfony will intercept this first
     * and handle the logout automatically. See logout in config/packages/security.yaml
     *
     * It's really a pity that Symfony doesn't have a facade that we can just call and have the user log out
     * because, as it is, this behaviour might be very difficult to decouple from Symfony and implement with
     * another framework, if we would switch frameworks.
     *
     * @throws \Exception
     */
    public function logout(): void
    {
        throw new \Exception('This should never be reached!');
    }
}
