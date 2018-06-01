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

namespace Acme\PhpExtension\Test\ObjectDispatcher;

use Acme\PhpExtension\Helper\ReflectionHelper;
use Acme\PhpExtension\ObjectDispatcher\Destination;
use Acme\PhpExtension\Test\AbstractUnitTest;

/**
 * @small
 */
final class AbstractDispatcherUnitTest extends AbstractUnitTest
{
    /**
     * @var DummyDispatcher
     */
    private $dispatcher;

    public function setUp(): void
    {
        $this->dispatcher = new DummyDispatcher();
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function adds_a_destination_in_the_correct_key(): void
    {
        $firstListenerMethod = 'doSomething';
        $secondListenerMethod = 'doSomethingElse';
        $thirdListenerMethod = 'doSomethingDifferent';

        $this->dispatcher->addDestination('message_name', [new DummyListener(), $firstListenerMethod]);
        $this->dispatcher->addDestination('message_name', [new DummyListener(), $secondListenerMethod]);
        $this->dispatcher->addDestination('another_message_name', [new DummyListener(), $thirdListenerMethod]);

        $objectDestinationMapper = ReflectionHelper::getProtectedProperty($this->dispatcher, 'objectDestinationMapper');

        self::assertArraySubset(
            [
                'message_name' => [
                    new Destination([new DummyListener(), $firstListenerMethod], 0),
                    new Destination([new DummyListener(), $secondListenerMethod], 0),
                ],
                'another_message_name' => [
                    new Destination([new DummyListener(), $thirdListenerMethod], 0),
                ],
            ],
            $objectDestinationMapper
        );
    }

    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function adds_a_destination_in_the_correct_priority(): void
    {
        $firstListenerMethod = 'doSomething';
        $secondListenerMethod = 'doSomethingElse';
        $thirdListenerMethod = 'doSomethingDifferent';

        $this->dispatcher->addDestination('message_name', [new DummyListener(), $firstListenerMethod]);
        $this->dispatcher->addDestination('message_name', [new DummyListener(), $secondListenerMethod], 10);
        $this->dispatcher->addDestination('message_name', [new DummyListener(), $thirdListenerMethod], -15);

        $objectDestinationMapper = ReflectionHelper::getProtectedProperty($this->dispatcher, 'objectDestinationMapper');

        self::assertArraySubset(
            [
                'message_name' => [
                    new Destination([new DummyListener(), $secondListenerMethod], 10),
                    new Destination([new DummyListener(), $firstListenerMethod], 0),
                    new Destination([new DummyListener(), $thirdListenerMethod], -15),
                ],
            ],
            $objectDestinationMapper
        );
    }

    /**
     * @test
     *
     * @dataProvider data_provider_for_has_destination_returns_correct_value
     */
    public function has_destination_returns_correct_value(string $messageName, bool $expectedResult): void
    {
        $firstListenerMethod = 'doSomething';
        $secondListenerMethod = 'doSomethingElse';
        $thirdListenerMethod = 'doSomethingDifferent';

        $this->dispatcher->addDestination('message_name', [new DummyListener(), $firstListenerMethod]);
        $this->dispatcher->addDestination('message_name', [new DummyListener(), $secondListenerMethod]);
        $this->dispatcher->addDestination('another_message_name', [new DummyListener(), $thirdListenerMethod]);

        self::assertSame($expectedResult, $this->dispatcher->hasDestination($messageName));
    }

    public function data_provider_for_has_destination_returns_correct_value(): array
    {
        return [
            ['message_name', true],
            ['message_name', true],
            ['another_message_name', true],
            ['inexistent_message_name', false],
        ];
    }
}
