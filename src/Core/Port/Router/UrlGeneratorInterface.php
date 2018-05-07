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

namespace Acme\App\Core\Port\Router;

/**
 * Although it looks like this port/adapter is something that should be in the `App\Core\Port` and `App\Infrastructure`
 * respectively, the fact is that this service is not used in the core, it is only used in the web UI. If we would
 * remove the web UI we would not need this interface at all.
 *
 * So following Uncle Bob packaging principles, we keep together the code that works together.
 *
 * We shamelessly copy this interface from \Symfony\Component\Routing\Generator\UrlGeneratorInterface
 * The reason is that, like for other code units, we want to be decoupled from the tools (ie. framework),
 * and we want to make our own tweeks to the URL generation, for example in this case we added type hints
 * to the method signature and removed the interface inheritance.
 * We also removed the `int` parameter, that would allow to inject any int, which could break the functionality,
 * and forced the injection of a UrlType enum object. This makes sure the application will not break.
 */
interface UrlGeneratorInterface
{
    public function generateUrl(string $routeName, array $parameters = [], UrlType $urlType = null): string;
}
