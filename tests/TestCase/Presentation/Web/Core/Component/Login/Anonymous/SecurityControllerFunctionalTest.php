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

namespace Acme\App\Test\TestCase\Presentation\Web\Core\Component\Login\Anonymous;

use Acme\App\Presentation\Web\Core\Port\Router\UrlGeneratorInterface;
use Acme\App\Presentation\Web\Core\Port\Router\UrlType;
use Acme\App\Test\Framework\AbstractFunctionalTest;
use Acme\App\Test\Framework\Data\RouteData;
use Acme\App\Test\Framework\Data\UserData;
use Symfony\Component\HttpFoundation\Response;

final class SecurityControllerFunctionalTest extends AbstractFunctionalTest
{
    /**
     * @test
     */
    public function login(): void
    {
        $urlGenerator = $this->getUrlGenerator();
        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request(
            'GET',
            $urlGenerator->generateUrl(RouteData::LOGIN, [], UrlType::absoluteUrl())
        );
        $form = $crawler->selectButton('Sign in')->form(
            [
                '_username' => UserData::ADMIN_USERNAME,
                '_password' => UserData::ADMIN_PASSWORD,
            ]
        );
        $crawler = $client->submit($form);

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $this->assertSame(
            $urlGenerator->generateUrl(RouteData::ANONYMOUS_POST_LIST, [], UrlType::absoluteUrl()),
            $crawler->getUri()
        );

        self::assertSame(
            0,
            $crawler->filter('.alert-danger')->count(),
            'Login with correct credentials shows an error message.'
        );
    }

    /**
     * @test
     */
    public function login_fail_shows_error_message(): void
    {
        $urlGenerator = $this->getUrlGenerator();
        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request(
            'GET',
            $urlGenerator->generateUrl(RouteData::LOGIN, [], UrlType::absoluteUrl())
        );
        $form = $crawler->selectButton('Sign in')->form(
            [
                '_username' => UserData::ADMIN_USERNAME,
                '_password' => 'wrong_password',
            ]
        );
        $crawler = $client->submit($form);

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $this->assertSame(
            $urlGenerator->generateUrl(RouteData::LOGIN, [], UrlType::absoluteUrl()),
            $crawler->getUri()
        );

        self::assertSame(
            1,
            $crawler->filter('.alert-danger')->count(),
            "Trying to login with wrong credentials doesn't show an error message."
        );
    }

    private function getUrlGenerator(): UrlGeneratorInterface
    {
        return self::getService(UrlGeneratorInterface::class);
    }
}
