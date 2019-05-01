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

namespace Acme\App\Test\Framework;

use Acme\App\Core\Port\Router\UrlGeneratorInterface;
use Acme\App\Core\Port\Router\UrlType;

trait RoutingAwareTestTrait
{
    /**
     * @var array
     */
    protected static $routeCache = [];

    /**
     * @return mixed
     */
    abstract protected function getService(string $service);

    protected function getRouter(): UrlGeneratorInterface
    {
        return $this->getService(UrlGeneratorInterface::class);
    }

    protected function generateUrl(
        string $name,
        array $parameters = [],
        ?UrlType $type = null
    ): string {
        $id = md5(json_encode(func_get_args()));

        if (array_key_exists($id, static::$routeCache)) {
            return static::$routeCache[$id];
        }

        return static::$routeCache[$id] = $this->getRouter()->generateUrl(
            $name,
            $parameters,
            $type ?? UrlType::absolutePath()
        );
    }

    /**
     * If we generate a UrlGeneratorInterface::ABSOLUTE_PATH in the test, we still end up with the domain in front
     * which makes it impossible to assert the target of a redirect.
     *
     * In that case use this:
     * $redirectedUrl = $this->generateUrlPath('public_service_request_details', []);
     * static::assertRedirectedTo($redirectedUrl);
     *
     * @param string $routeName
     * @param array $parameters
     *
     * @return string
     */
    protected function generateUrlPath(string $routeName, array $parameters = []): string
    {
        return preg_replace('|^(https?://[^/]+)(/.*)|i', '\2', $this->generateUrl($routeName, $parameters));
    }
}
