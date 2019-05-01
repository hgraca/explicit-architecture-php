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
use Acme\App\Core\SharedKernel\Component\User\Domain\User\UserId;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use function is_string;

final class OauthAccessTokenRepository implements AccessTokenRepositoryInterface
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

    /**
     * @param ScopeEntityInterface[] $scopes
     * @param UserId $userIdentifier
     */
    public function getNewToken(
        ClientEntityInterface $clientEntity,
        array $scopes,
        $userIdentifier = null
    ): AccessTokenEntityInterface {
        return new OauthAccessToken(
            is_string($userIdentifier) ? new UserId($userIdentifier) : $userIdentifier, $clientEntity, $scopes
        );
    }

    public function persistNewAccessToken(AccessTokenEntityInterface $oauthAccessToken): void
    {
        $this->persistenceService->upsert($oauthAccessToken);
    }

    /**
     * @param string $tokenId
     */
    public function revokeAccessToken($tokenId): void
    {
        try {
            $oauthAccessToken = $this->find($tokenId);
        } catch (EmptyQueryResultException $e) {
            return;
        }

        $oauthAccessToken->revoke();
        $this->persistenceService->upsert($oauthAccessToken);
    }

    /**
     * @param string $tokenId
     */
    public function isAccessTokenRevoked($tokenId): ?bool
    {
        try {
            return $this->find($tokenId)->isRevoked();
        } catch (EmptyQueryResultException $e) {
            return true;
        }
    }

    private function find(string $tokenId): OauthAccessToken
    {
        $dqlQuery = $this->dqlQueryBuilder->create(OauthAccessToken::class)
            ->where('OauthAccessToken.identifier = :identifier')
            ->setParameter('identifier', $tokenId)
            ->build();

        return $this->queryService->query($dqlQuery)->getSingleResult();
    }
}
