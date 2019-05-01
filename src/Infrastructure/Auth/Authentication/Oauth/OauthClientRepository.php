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
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

final class OauthClientRepository implements ClientRepositoryInterface
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
     * @param string $clientIdentifier The client's identifier
     * @param string|null $grantType The grant type used (if sent)
     * @param string|null $clientSecret The client's secret (if sent)
     * @param bool $mustValidateSecret If true the client must attempt to validate the secret if the client
     *                                        is confidential
     */
    public function getClientEntity(
        $clientIdentifier,
        $grantType = null,
        $clientSecret = null,
        $mustValidateSecret = true
    ): ?ClientEntityInterface {
        try {
            $oauthClient = $this->findActive($clientIdentifier);
        } catch (EmptyQueryResultException $e) {
            return null;
        }

        if ($mustValidateSecret && !hash_equals($oauthClient->getSecret(), (string) $clientSecret)) {
            return null;
        }

        return $oauthClient;
    }

    private function findActive(string $clientIdentifier): OauthClient
    {
        $dqlQuery = $this->dqlQueryBuilder->create(OauthClient::class)
            ->where('OauthClient.identifier = :clientIdentifier')
            ->andWhere('OauthClient.active = :active')
            ->setParameter('clientIdentifier', $clientIdentifier)
            ->setParameter('active', true)
            ->build();

        return $this->queryService->query($dqlQuery)->getSingleResult();
    }
}
