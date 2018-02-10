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

namespace Acme\PhpExtension\Enum;

use Acme\PhpExtension\Exception\AcmeRuntimeException;
use Acme\PhpExtension\Helper\ClassHelper;
use BadMethodCallException;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;

abstract class AbstractEnum
{
    protected $value;

    protected function __construct($value)
    {
        if (!$this->isValid($value)) {
            $message = 'Value [%s] is not matching any valid value of class "%s". Valid values are [%s].';

            throw new InvalidArgumentException(sprintf(
                $message,
                $value,
                $this->getClassName(),
                self::getValidOptionsAsString()
            ));
        }

        $this->value = $value;
    }

    /**
     * @return static
     */
    public static function get($value): self
    {
        return new static($value);
    }

    /**
     * @return static
     */
    public static function __callStatic(string $methodName, array $arguments): self
    {
        foreach (self::getConstants() as $option => $value) {
            $expectedMethodName = ClassHelper::toCamelCase($option);
            if ($expectedMethodName === $methodName) {
                return new static($value);
            }
        }

        throw new BadMethodCallException(sprintf('%s::%s() does not exist', static::class, $methodName));
    }

    public function __call(string $methodName, array $arguments)
    {
        foreach (self::getConstants() as $option => $value) {
            $isaMethodName = 'is' . ClassHelper::toStudlyCase($option);
            if ($isaMethodName === $methodName) {
                return $this->equals(static::get($value));
            }
        }

        throw new BadMethodCallException(sprintf('%s::%s() does not exist', static::class, $methodName));
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    public function getKey(): string
    {
        return \array_search($this->value, self::getConstants(), true);
    }

    public function equals(self $other): bool
    {
        return \get_class($other) === \get_class($this) && $other->value === $this->value;
    }

    public static function getValidOptions(): array
    {
        return \array_values(self::getConstants());
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }

    protected function isValid($value): bool
    {
        return \in_array($value, self::getValidOptions(), true);
    }

    protected function getClassName(): string
    {
        return ClassHelper::extractCanonicalClassName(static::class);
    }

    private static function getConstants(): array
    {
        try {
            return (new ReflectionClass(static::class))->getConstants();
        } catch (ReflectionException $e) {
            throw new AcmeRuntimeException(
                'Error getting the constants of the Enum: ' . static::class,
                $e->getCode(),
                $e
            );
        }
    }

    private static function getValidOptionsAsString(): string
    {
        return implode(
            ', ',
            array_map(function ($option) {
                return var_export($option, true);
            }, self::getValidOptions())
        );
    }
}
