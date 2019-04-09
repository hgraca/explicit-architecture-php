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

namespace Acme\App\Test\TestCase\Presentation\Api\Rest\Oauth;

use Acme\App\Build\Fixture\Doctrine\OauthClientFixtures;
use Acme\App\Build\Fixture\Doctrine\OauthScopeFixtures;
use Acme\App\Build\Fixture\Doctrine\UserFixtures;
use Acme\App\Core\Port\Persistence\DQL\DqlQueryBuilderInterface;
use Acme\App\Core\Port\Persistence\QueryServiceRouterInterface;
use Acme\App\Infrastructure\Auth\Authentication\Oauth\OauthClient;
use Acme\App\Test\Framework\AbstractFunctionalTest;
use Symfony\Component\HttpFoundation\Response;
use function json_decode;

/**
 * @large
 *
 * @internal
 */
final class AccessTokenControllerFunctionalTest extends AbstractFunctionalTest
{
    private const GRANT_PASSWORD = 'password';

    /**
     * @test
     */
    public function requesting_a_protected_route_without_valid_access_token_fails(): void
    {
        $tokenData = [
            'token_type' => 'Bearer',
            'expires_in' => 3600,
            'access_token' => 'dummyAccessToken',
            'refresh_token' => 'def50200532d37b623ad7bd15c5267eeac18542f6723eecbdaa46cb73a63c9c46f04ee9940e5aff289655d43f67690a334cce9d0e287454e2cc6806c03816a20b76acc71007d65285375238315c34361c518066b6457dec615424b452759124cc5f1bb5797e391eea11c4a0e8181163b641bb68521467202f250eee7f3051b31ad332c1ab2baf862f5dddbc9cf522475a6d5083bff10c1376bc0fd95d84e36d9e139c63688e71fd13ec9e6a63c9467d095a12f46572a4b1a5adcfc23d0bf0699974c98597647ef5d3d6b2bd68eb2ab5823b52a409a7a24d1db80e214c301d1a98de05f3a8ad3f4734dd45b54fb0a72417897ab7e1479b175a0a4a1e4ea7ec12021a619999ce078768face2afd3b3ebd4efb127b84f8f69195a1dcc627406702bdea302342d7e67291755b0d0fb7912ddd14f6e5116524a10b278ca868ed76bb23d1e1a8d6f0d3b0a34d2774cad6358b824cc18bd0423458ff0283bb08eb9de16e27731c58a6915d06a86e0003b620ea3072d0e2854cf18330821a7e07d1ee33eee661e6d',
        ];

        $response = $this->requestProtectedRoute($tokenData['access_token']);

        self::assertSame(401, $response->getStatusCode(), $response->getContent());
    }

    /**
     * @test
     */
    public function requesting_an_access_token_gives_a_token_that_works(): void
    {
        $tokenData = $this->requestAccessToken(
            $this->getOauthClient(OauthClientFixtures::CLIENT_WEB_APP),
            self::GRANT_PASSWORD,
            OauthScopeFixtures::SCOPE_ADMIN,
            UserFixtures::TOM_USERNAME,
            UserFixtures::TOM_PASSWORD,
            );

        $response = $this->requestProtectedRoute($tokenData['access_token']);

        self::assertSame(200, $response->getStatusCode(), $response->getContent());
    }

    private function getOauthClient(string $appName): OauthClient
    {
        /** @var QueryServiceRouterInterface $queryService */
        $queryService = self::getService(QueryServiceRouterInterface::class);
        /** @var DqlQueryBuilderInterface $queryBuilder */
        $queryBuilder = self::getService(DqlQueryBuilderInterface::class);
        $queryBuilder->create(OauthClient::class)
            ->where('OauthClient.name = :appName')
            ->setParameter('appName', $appName);

        return $queryService->query($queryBuilder->build())->getSingleResult();
    }

    private function requestAccessToken(
        OauthClient $oauthClient,
        string $grantType,
        $scope,
        $username,
        $password
    ): array {
        $response = $this->requestRoute(
            'POST',
            'oauth_get_access_token',
            [],
            [
                'grant_type' => $grantType,
                'client_id' => (string) $oauthClient->getIdentifier(),
                'client_secret' => $oauthClient->getSecret(),
                'scope' => $scope,
                'username' => $username,
                'password' => $password,
            ],
            );

        self::assertSame(200, $response->getStatusCode(), $response->getContent());
        $contentAsArray = json_decode($response->getContent(), true);
        self::assertArrayHasKey('token_type', $contentAsArray);
        self::assertArrayHasKey('expires_in', $contentAsArray);
        self::assertArrayHasKey('access_token', $contentAsArray);
        self::assertArrayHasKey('refresh_token', $contentAsArray);

        return $contentAsArray;
    }

    private function requestProtectedRoute(string $accessToken): Response
    {
        return $this->requestRoute(
            'GET',
            'test_oauth_get_access_token',
            [],
            [],
            null,
            [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $accessToken,
            ],
        );
    }
}
