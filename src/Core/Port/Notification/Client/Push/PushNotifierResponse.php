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

namespace Acme\App\Core\Port\Notification\Client\Push;

use Psr\Http\Message\ResponseInterface;

final class PushNotifierResponse
{
    /**
     * @var int
     */
    private $code;

    /**
     * @var mixed
     */
    private $response;

    public function __construct(ResponseInterface $response)
    {
        $this->code = $response->getStatusCode();
        $this->response = json_decode($response->getBody()->getContents());
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getResponse()
    {
        return $this->response;
    }
}
