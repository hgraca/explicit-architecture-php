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

use Acme\App\Core\Port\Persistence\DQL\DqlQueryBuilderInterface;
use Acme\App\Core\Port\Persistence\Exception\EmptyQueryResultException;
use Acme\App\Core\Port\Persistence\PersistenceServiceInterface;
use Acme\App\Core\Port\Persistence\QueryServiceRouterInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;

final class OauthRefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    /**
     * @var DqlQueryBuilderInterface
     */
    private $dqlQueryBuilder;

    /**
     * @var QueryServiceRouterInterface
     */
    private $queryService;

    /**
     * @var PersistenceServiceInterface
     */
    private $persistenceService;

    public function __construct(
        DqlQueryBuilderInterface $dqlQueryBuilder,
        QueryServiceRouterInterface $queryService,
        PersistenceServiceInterface $persistenceService
    ) {
        $this->dqlQueryBuilder = $dqlQueryBuilder;
        $this->queryService = $queryService;
        $this->persistenceService = $persistenceService;
    }

    public function getNewRefreshToken(): RefreshTokenEntityInterface
    {
        return new OauthRefreshToken();
    }

    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshToken): void
    {
        $this->persistenceService->upsert($refreshToken);
    }

    /**
     * @param OauthRefreshTokenId $refreshTokenId
     */
    public function revokeRefreshToken($refreshTokenId): void
    {
        try {
            $refreshToken = $this->find($refreshTokenId);
        } catch (EmptyQueryResultException $e) {
            return;
        }

        $refreshToken->revoke();
        $this->persistenceService->upsert($refreshToken);
    }

    /**
     * @param OauthRefreshTokenId $refreshTokenId
     */
    public function isRefreshTokenRevoked($refreshTokenId): bool
    {
        try {
            $refreshToken = $this->find($refreshTokenId);
        } catch (EmptyQueryResultException $e) {
            return true;
        }

        return $refreshToken->isRevoked()
            ?: $refreshToken->getAccessToken()->isRevoked();
    }

    private function find(OauthRefreshTokenId $refreshTokenId): OauthRefreshToken
    {
        $dqlQuery = $this->dqlQueryBuilder->create(OauthRefreshToken::class)
            ->where('OauthAccessToken.identifier = :identifier')
            ->setParameter('identifier', $refreshTokenId)
            ->build();

        return $this->queryService->query($dqlQuery)->getSingleResult();
    }
}
