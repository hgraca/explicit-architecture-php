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

namespace Acme\App\Core\Port\Notification\Client\Email;

/**
 * @author Marijn Koesen
 * @author Herberto Graca <herberto.graca@gmail.com>
 */
class EmailAddress
{
    /**
     * @var string
     */
    private $email;

    /**
     * @var string|null
     */
    private $name;

    public function __construct(string $email, string $name = null)
    {
        $this->email = $email;
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function __toString(): string
    {
        if (!$this->name) {
            return $this->email;
        }

        return sprintf('%s <%s>', $this->name, $this->email);
    }
}
