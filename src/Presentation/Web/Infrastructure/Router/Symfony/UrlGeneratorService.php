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

namespace Acme\App\Presentation\Web\Infrastructure\Router\Symfony;

use Acme\App\Presentation\Web\Core\Port\Router\UrlGeneratorInterface;
use Acme\App\Presentation\Web\Core\Port\Router\UrlType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as SymfonyUrlGeneratorInterface;

final class UrlGeneratorService implements UrlGeneratorInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(SymfonyUrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function generateUrl(
        string $routeName,
        array $parameters = [],
        UrlType $urlType = null
    ): string {
        if (!$urlType) {
            $urlType = UrlType::absolutePath();
        }

        return $this->urlGenerator->generate($routeName, $parameters, $urlType->getValue());
    }
}
