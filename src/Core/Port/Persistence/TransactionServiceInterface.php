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

namespace Acme\App\Core\Port\Persistence;

interface TransactionServiceInterface
{
    public function startTransaction(): void;

    /**
     * This is is when the ORM writes all staged changes, to the DB
     *      so we should do this only once in a request, and only if the use case command was successful.
     */
    public function finishTransaction(): void;

    public function rollbackTransaction(): void;
}
