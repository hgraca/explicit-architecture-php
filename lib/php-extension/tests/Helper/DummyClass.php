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

final class DummyClass extends DummyClassParent
{
    /**
     * @var int
     */
    private $var;

    /**
     * @var string
     */
    private $testProperty = 'FooBar';

    /**
     * @var int|null
     */
    private $anotherVar;

    public function __construct(int $var = 1, int $parentVar = 2)
    {
        parent::__construct($parentVar);
        $this->var = $var;

        $this->anotherVar = 1;
    }

    public function getTestProperty(): string
    {
        return $this->testProperty;
    }

    public function getAnotherVar(): ?int
    {
        return $this->anotherVar;
    }

    protected function getVarProtected(): int
    {
        return $this->var;
    }

    private function getVarPrivate(): ?int
    {
        return $this->var;
    }
}
