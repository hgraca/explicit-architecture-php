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

namespace Acme\App\Core\Port\Auth\Authentication;

use Acme\App\Core\SharedKernel\Exception\AppRuntimeException;
use Throwable;

final class AuthenticationException extends AppRuntimeException
{
    /**
     * @var string
     */
    private $messageKey;

    /**
     * @var array
     */
    private $messageData;

    public function __construct(
        string $message = '',
        int $code = 0,
        Throwable $previous = null,
        string $messageKey = '',
        array $messageData = []
    ) {
        parent::__construct($message, $code, $previous);

        $this->messageKey = $messageKey;
        $this->messageData = $messageData;
    }

    public function getMessageKey(): string
    {
        return $this->messageKey;
    }

    public function getMessageData(): array
    {
        return $this->messageData;
    }
}
