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

namespace Acme\PhpExtension\Test;

use Acme\PhpExtension\ConstructableFromArrayInterface;
use Acme\PhpExtension\ConstructableFromArrayTrait;

final class DummyConstructableFromArray implements ConstructableFromArrayInterface
{
    use ConstructableFromArrayTrait;

    /**
     * @var mixed
     */
    private $prop1;

    /**
     * @var int
     */
    private $prop2 = 9;

    public function __construct($prop1, $prop2 = 0)
    {
        $this->prop1 = $prop1;
        $this->prop2 = $prop2;
    }

    /**
     * @return mixed
     */
    public function getProp1()
    {
        return $this->prop1;
    }

    public function getProp2(): int
    {
        return $this->prop2;
    }
}
