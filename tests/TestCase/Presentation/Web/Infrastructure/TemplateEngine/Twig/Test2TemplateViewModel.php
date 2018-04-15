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

namespace Acme\App\Test\TestCase\Presentation\Web\Infrastructure\TemplateEngine\Twig;

use Acme\App\Presentation\Web\Core\Port\TemplateEngine\TemplateViewModelInterface;
use DateTime;

final class Test2TemplateViewModel implements TemplateViewModelInterface
{
    /**
     * @var array
     */
    private $array;

    /**
     * @var string
     */
    private $string;

    /**
     * @var int
     */
    private $get;

    /**
     * @var DateTime
     */
    private $object;

    public function __construct(array $array, string $string, int $get, DateTime $object)
    {
        $this->array = $array;
        $this->string = $string;
        $this->get = $get;
        $this->object = $object;
    }

    public function getArray(): array
    {
        return $this->array;
    }

    public function getString(): string
    {
        return $this->string;
    }

    public function getGet(): int
    {
        return $this->get;
    }

    public function getObject(): DateTime
    {
        return $this->object;
    }

    public function hasFunction(): bool
    {
        return true;
    }

    public function isBool(): bool
    {
        return false;
    }

    public function shouldBeAMonkey(): bool
    {
        return true;
    }
}
