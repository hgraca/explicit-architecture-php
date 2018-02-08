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

namespace Acme\PhpExtension\Test\Helper;

class DummyClassParent
{
    /**
     * @var int
     */
    private $parentVar;

    /**
     * @var string
     */
    private $parentTestProperty = 'FooBar';

    public function __construct(int $parentVar)
    {
        $this->parentVar = $parentVar;
    }

    public function getParentTestProperty(): string
    {
        return $this->parentTestProperty;
    }
}
