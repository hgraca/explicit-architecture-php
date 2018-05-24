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

use Acme\App\Core\SharedKernel\Component\User\Domain\User\UserId;

/**
 * @author Alexander Malyk
 * @author Herberto Graca <herberto.graca@gmail.com>
 */
final class PushNotification
{
    /**
     * @var string
     */
    private $shortName;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $message;

    /**
     * @var UserId
     */
    private $userId;

    /**
     * @var string[]
     */
    private $data;

    public function __construct(string $shortName, string $title, string $message, UserId $userId, array $data = [])
    {
        $this->shortName = $shortName;
        $this->title = $title;
        $this->message = $message;
        $this->userId = $userId;
        $this->data = $data;
    }

    public function getUserId(): UserId
    {
        return $this->userId;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getShortName(): string
    {
        return $this->shortName;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
