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

use Acme\App\Presentation\Web\Core\Port\Paginator\PaginatorFactoryInterface;
use Acme\App\Presentation\Web\Core\Port\Paginator\PaginatorInterface;

final class PagerfantaPaginatorFactory implements PaginatorFactoryInterface
{
    public function createPaginator(array $data): PaginatorInterface
    {
        return new PagerfantaPaginator($data);
    }
}
