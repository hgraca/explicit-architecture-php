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

namespace Acme\App\Test\Framework;

use Acme\App\Test\Framework\Mock\MockTrait;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

/**
 * A unit test will test a method, class or set of classes in isolation from the tools and delivery mechanisms.
 * How isolated the test needs to be, it depends on the situation and how you decide to test the application.
 * However, it is important to note that these tests do not need to boot the application.
 */
abstract class AbstractUnitTest extends TestCase
{
    use MockeryPHPUnitIntegration;
    use MockTrait;
}
