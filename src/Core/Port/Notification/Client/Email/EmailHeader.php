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

namespace Acme\App\Core\Port\Notification\Client\Email;

use InvalidArgumentException;

/**
 * @author Herberto Graca <herberto.graca@gmail.com>
 * @author Jeroen Van Den Heuvel
 * @author Marijn Koesen
 */
class EmailHeader
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $value;

    public function __construct(string $name, string $value = '')
    {
        $this->name = $name;
        $this->setValue($value);
    }

    public function getName(): string
    {
        return $this->name;
    }

    protected function setValue(string $value): void
    {
        $this->validateValue($value);
        $this->value = $value;
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function validateValue(string $value): void
    {
        if (\is_object($value) && !\method_exists($value, '__toString')) {
            throw new InvalidArgumentException('Object cannot be represented as a string');
        }

        if (!\is_object($value) && !\is_scalar($value) && !empty($value)) {
            throw new InvalidArgumentException('Given value is not a scalar');
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
