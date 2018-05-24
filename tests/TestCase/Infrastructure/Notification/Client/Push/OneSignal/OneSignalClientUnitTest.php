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

namespace Acme\App\Test\TestCase\Infrastructure\Notification\Client\Push\OneSignal;

use Acme\App\Core\Port\Notification\Client\Push\PushNotification;
use Acme\App\Core\SharedKernel\Component\User\Domain\User\UserId;
use Acme\App\Infrastructure\Notification\Client\Push\OneSignal\OneSignalClient;
use Acme\App\Test\Framework\AbstractUnitTest;
use GuzzleHttp\ClientInterface;
use Mockery;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

final class OneSignalClientUnitTest extends AbstractUnitTest
{
    /**
     * @test
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendNotification(): void
    {
        $appIDKey = 'app_id';
        $restAPIKey = 'rest_api_key';
        $language = 'en';
        $userId = new UserId();
        $message = 'Hello Alexey!';
        $content = 'some content';

        $bodyMock = Mockery::mock(StreamInterface::class);
        $bodyMock->shouldReceive('getContents')->andReturn($content);

        $httpResponseMock = Mockery::mock(ResponseInterface::class);
        $httpResponseMock->shouldReceive('getStatusCode')->once()->andReturn(200);
        $httpResponseMock->shouldReceive('getBody')->once()->andReturn($bodyMock);

        $httpClientMock = Mockery::mock(ClientInterface::class);
        $httpClientMock->shouldReceive('request')->once()->with(
            'POST',
            sprintf('%s%s', OneSignalClient::API_BASE_URI, OneSignalClient::ENDPOINT_NOTIFICATIONS),
            [
                'headers' => [
                        'Authorization' => 'Basic ' . $restAPIKey,
                        'Content-Type' => 'application/json; charset=utf-8',
                    ],
                'form_params' => [],
                'json' => [
                        'app_id' => $appIDKey,
                        'filters' => [
                                0 => [
                                    'field' => 'tag',
                                    'key' => OneSignalClient::TAG_USER_ID,
                                    'relation' => '=',
                                    'value' => (string) $userId,
                                ],
                            ],
                        'contents' => [
                                $language => $message,
                            ],
                        'headings' => [
                                $language => '',
                            ],
                        'data' => [
                                'short_name' => '',
                                'dummy' => 'yes',
                            ],
                    ],
            ]
        )
            ->andReturn($httpResponseMock);

        $oneSignalClient = new OneSignalClient($appIDKey, $restAPIKey, $httpClientMock);

        $oneSignalClient->sendNotification(new PushNotification('', '', $message, $userId, ['dummy' => 'yes']));
    }
}
