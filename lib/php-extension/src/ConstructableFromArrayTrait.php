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

namespace Acme\PhpExtension;

use Exception;
use ReflectionException;
use ReflectionMethod;

trait ConstructableFromArrayTrait
{
    /**
     * @throws ReflectionException
     * @throws Exception
     *
     * @return static
     */
    public static function fromArray(array $data)
    {
        $reflectionMethod = new ReflectionMethod(static::class, '__construct');
        $reflectionParameterList = $reflectionMethod->getParameters();

        $argumentList = [];
        foreach ($reflectionParameterList as $reflectionParameter) {
            $parameterName = $reflectionParameter->getName();
            if (
                !\array_key_exists($parameterName, $data)
                && !$reflectionParameter->isOptional()
            ) {
                throw new Exception(
                    "Can't instantiate '" . static::class . ' from an array'
                    . " because argument '$parameterName' is missing and it's not optional."
                    . ' Available argument names: ' . implode(', ', \array_keys($data))
                );
            }

            $argument = $data[$parameterName] ?? $reflectionParameter->getDefaultValue();
            if ($reflectionParameter->isVariadic() && \is_array($argument)) {
                $argumentList = \array_merge($argumentList, $argument);
            } else {
                $argumentList[] = $argument;
            }
        }

        return new static(...$argumentList);
    }
}
