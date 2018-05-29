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

namespace Acme\App\Infrastructure\Notification\Client\Email\SwiftMailer\Mapper;

use Acme\App\Core\Port\Notification\Client\Email\Email;

/**
 * @author Marijn Koesen
 * @author Herberto Graca <herberto.graca@gmail.com>
 */
interface EmailMapper
{
    /**
     * Map an existing Email to another format
     *
     * @return mixed
     */
    public function map(Email $message);
}
