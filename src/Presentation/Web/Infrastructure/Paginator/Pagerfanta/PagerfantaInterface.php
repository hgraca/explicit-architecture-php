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

namespace Acme\App\Presentation\Web\Infrastructure\Paginator\Pagerfanta;

use Pagerfanta\PagerfantaInterface as RealPagerfantaInterface;

/**
 * We need this interface because the real PagerfantaInterface does not define any methods.
 */
interface PagerfantaInterface extends RealPagerfantaInterface
{
    public function haveToPaginate(): bool;

    public function getCurrentPage(): int;

    public function getNbPages(): int;

    public function hasPreviousPage(): bool;

    public function hasNextPage(): bool;

    public function getNextPage(): int;

    public function getPreviousPage(): int;
}
