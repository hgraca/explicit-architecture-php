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
use Acme\App\Core\Port\Persistence\QueryServiceRouterInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

final class OauthScopeRepository implements ScopeRepositoryInterface
{
    /**
     * @var DqlQueryBuilderInterface
     */
    private $dqlQueryBuilder;

    /**
     * @var QueryServiceRouterInterface
     */
    private $queryService;

    public function __construct(
        DqlQueryBuilderInterface $dqlQueryBuilder,
        QueryServiceRouterInterface $queryService
    ) {
        $this->dqlQueryBuilder = $dqlQueryBuilder;
        $this->queryService = $queryService;
    }

    /**
     * {@inheritdoc}
     */
    public function getScopeEntityByIdentifier($identifier): ?ScopeEntityInterface
    {
        return $this->find($identifier);
    }

    /**
     * Given a client, grant type and optional user identifier validate the set of scopes requested are valid and
     * optionally append additional scopes or remove requested scopes.
     *
     * @param ScopeEntityInterface[] $scopes
     * @param string $grantType
     * @param string|null $userIdentifier
     *
     * @return ScopeEntityInterface[]
     */
    public function finalizeScopes(
        array $scopes,
        $grantType,
        ClientEntityInterface $clientEntity,
        $userIdentifier = null
    ): array {
        $filteredScopes = [];

        foreach ($scopes as $scope) {
            if (OauthScope::hasScope($scope->getIdentifier())) {
                $filteredScopes[] = $scope;
            }
        }

        return $filteredScopes;
    }

    private function find(string $oauthScopeId): ?OauthScope
    {
        $dqlQuery = $this->dqlQueryBuilder->create(OauthScope::class)
            ->where('OauthScope.identifier = :identifier')
            ->setParameter('identifier', $oauthScopeId)
            ->build();

        try {
            return $this->queryService->query($dqlQuery)->getSingleResult();
        } catch (EmptyQueryResultException $e) {
            return null;
        }
    }
}
