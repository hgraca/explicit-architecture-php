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

namespace Acme\App\Infrastructure\Notification\Client\Push\OneSignal;

use Acme\App\Core\Port\Notification\Client\Push\Exception\PushNotifierException;
use Acme\App\Core\Port\Notification\Client\Push\PushNotification;
use Acme\App\Core\Port\Notification\Client\Push\PushNotifierInterface;
use Acme\App\Core\Port\Notification\Client\Push\PushNotifierResponse;
use Acme\App\Core\SharedKernel\Component\User\Domain\User\UserId;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

/**
 * @author Herberto Graca <herberto.graca@gmail.com>
 * @author Alexander Malyk
 */
final class OneSignalClient implements PushNotifierInterface
{
    public const API_BASE_URI = 'https://onesignal.com/api/v1/';
    private const HTTP_TIMEOUT = 30;

    public const ENDPOINT_NOTIFICATIONS = 'notifications';

    private const RELATION_EQUAL = '=';

    private const FIELD_TAG = 'tag';

    // This tag name needs to stay in sync with what is used in the mobile app
    public const TAG_USER_ID = 'userId';

    private const DEFAULT_LANGUAGE = 'en';

    /**
     * @var string
     */
    private $appIDKey;

    /**
     * @var string
     */
    private $restAPIKey;

    /**
     * @var ClientInterface
     */
    private $httpClient;

    public function __construct(
        string $appIDKey,
        string $restAPIKey,
        ClientInterface $httpClient = null
    ) {
        $this->appIDKey = $appIDKey;
        $this->restAPIKey = $restAPIKey;
        $this->httpClient = $httpClient ?? new Client(['base_uri' => self::API_BASE_URI, 'timeout' => self::HTTP_TIMEOUT]);
    }

    /**
     * @throws GuzzleException
     */
    public function sendNotification(PushNotification $pushNotification): PushNotifierResponse
    {
        return $this->execute(
            self::ENDPOINT_NOTIFICATIONS,
            'POST',
            [
                'headers' => [
                    'Content-Type' => 'application/json; charset=utf-8',
                ],
                'json' => [
                    'app_id' => $this->appIDKey,
                    'filters' => [
                        [
                            'field' => self::FIELD_TAG,
                            'key' => self::TAG_USER_ID,
                            'relation' => self::RELATION_EQUAL,
                            'value' => $this->createUserIdTag($pushNotification->getUserId()),
                        ],
                    ],
                    'contents' => [self::DEFAULT_LANGUAGE => $pushNotification->getMessage()],
                    'headings' => [self::DEFAULT_LANGUAGE => $pushNotification->getTitle()],
                    'data' => array_merge($pushNotification->getData(), ['short_name' => $pushNotification->getShortName()]),
                ],
            ]
        );
    }

    /**
     * @throws PushNotifierException
     * @throws GuzzleException
     */
    private function execute(string $url, string $method, array $httpClientOptions = []): PushNotifierResponse
    {
        $defaultHttpClientOptions = [
            'headers' => [
                'Authorization' => sprintf('Basic %s', $this->restAPIKey),
            ],
            'form_params' => [],
        ];
        $endpointURL = sprintf('%s%s', self::API_BASE_URI, $url);
        $httpClientOptions = array_replace_recursive($defaultHttpClientOptions, $httpClientOptions);

        try {
            $response = $this->httpClient->request(mb_strtoupper($method), $endpointURL, $httpClientOptions);
        } catch (ClientException $exception) {
            $response = $exception->getResponse();
        } catch (Exception $exception) {
            throw new PushNotifierException($exception->getMessage(), 0, $exception);
        }

        return new PushNotifierResponse($response);
    }

    private function createUserIdTag(UserId $userId): string
    {
        return (string) $userId;
    }
}
