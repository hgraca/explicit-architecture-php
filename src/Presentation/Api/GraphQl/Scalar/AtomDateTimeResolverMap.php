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

namespace Acme\App\Presentation\Api\GraphQl\Scalar;

use DateTimeImmutable;
use DateTimeInterface;
use GraphQL\Language\AST\StringValueNode;
use Overblog\GraphQLBundle\Resolver\ResolverMap as BaseResolverMap;

final class AtomDateTimeResolverMap extends BaseResolverMap
{
    protected function map(): array
    {
        return [
            'AtomDateTime' => [
                self::SERIALIZE => function (DateTimeInterface $value) {
                    return $value->format(DateTimeInterface::ATOM);
                },
                self::PARSE_VALUE => function (string $value) {
                    return new DateTimeImmutable($value);
                },
                self::PARSE_LITERAL => function (StringValueNode $valueNode) {
                    return new DateTimeImmutable($valueNode->value);
                },
            ],
        ];
    }
}
