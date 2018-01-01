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

interface QueryBuilderInterface
{
    /**
     * Use constants to define configuration options that rarely change instead
     * of specifying them in config.
     *
     * See https://symfony.com/doc/current/best_practices/configuration.html#constants-vs-configuration-options
     */
    public const DEFAULT_MAX_RESULTS = 30;

    public function build(): QueryInterface;
}
