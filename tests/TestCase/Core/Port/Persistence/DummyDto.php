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

namespace Acme\App\Test\TestCase\Core\Port\Persistence;

use Acme\PhpExtension\ConstructableFromArrayInterface;
use Acme\PhpExtension\ConstructableFromArrayTrait;

final class DummyDto implements ConstructableFromArrayInterface
{
    use ConstructableFromArrayTrait;

    /**
     * @var int
     */
    private $a;

    /**
     * @var string
     */
    private $b;

    public function __construct(int $a, string $b)
    {
        $this->a = $a;
        $this->b = $b;
    }

    public function getA(): int
    {
        return $this->a;
    }

    public function getB(): string
    {
        return $this->b;
    }
}
