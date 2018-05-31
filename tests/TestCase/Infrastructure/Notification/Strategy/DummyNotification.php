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

namespace Acme\App\Test\TestCase\Infrastructure\Notification\Strategy;

use Acme\App\Core\Port\Notification\NotificationInterface;
use Acme\App\Core\SharedKernel\Component\User\Domain\User\UserId;

final class DummyNotification implements NotificationInterface
{
    /**
     * @var string
     */
    private $hash;

    /**
     * @var UserId
     */
    private $userId;

    public function __construct(string $hash, UserId $userId = null)
    {
        $this->hash = $hash;
        $this->userId = $userId;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getDestinationUserId(): UserId
    {
        return $this->userId;
    }
}
