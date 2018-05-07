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

namespace Acme\App\Test\TestCase\Infrastructure\TemplateEngine\Twig;

use Acme\App\Core\Port\TemplateEngine\TemplateViewModelInterface;

final class Test1TemplateViewModel implements TemplateViewModelInterface
{
    /**
     * @var string
     */
    private $var1;

    /**
     * @var string
     */
    private $var2;

    public function __construct(string $var1, string $var2)
    {
        $this->var1 = $var1;
        $this->var2 = $var2;
    }

    public function getVar1(): string
    {
        return $this->var1;
    }

    public function getVar2(): string
    {
        return $this->var2;
    }
}
